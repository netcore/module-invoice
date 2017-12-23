<?php

namespace Modules\Invoice\PassThroughs\Invoice;

use Illuminate\Support\Facades\DB;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\PassThroughs\PassThrough;
use Netcore\Translator\Helpers\TransHelper;

class Storage extends PassThrough
{
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var Collection
     */
    private $languages;

    /**
     * Storage constructor.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     * @param array $requestData
     * @return Invoice
     */
    public function update(Array $requestData): Invoice
    {
        $invoice = DB::transaction(function () use ($requestData) {
            return $this->transaction($requestData);
        });

        return $invoice;
    }

    /**
     * @param array $requestData
     * @return Invoice
     */
    private function transaction(Array $requestData): Invoice
    {
        $invoice = $this->invoice;

        /**
         * Regular data
         */
        $invoice->forceFill(array_only($requestData, [
            'invoice_nr',
            'sender_data',
            'receiver_data',
            'vat'
        ]));

        /**
         * Relations
         */
        $relations = config('netcore.module-invoice.relations');
        $belongsTo = collect($relations)->where('type', 'belongsTo');
        foreach($belongsTo as $relation) {
            $foreignKey = array_get($relation, 'foreignKey');
            $value = array_get($requestData, $foreignKey);
            $invoice->$foreignKey = $value;
        }

        // Save regular data and foreign keys for relations
        $invoice->save();

        /**
         * Create/update items
         */
        $backendItems = $invoice->items;
        $frontendItems = array_get($requestData, 'items', []);
        $itemIdsBefore = $backendItems->pluck('id')->toArray();
        $receivedItemIds = [];
        foreach($frontendItems as $frontendId => $frontendItem) {

            $regularItemData = array_only($frontendItem, [
                'price_without_vat',
                'price_with_vat',
                'quantity'
            ]);

            $backendItem = $backendItems->where('id', $frontendId)->first();
            if(!$backendItem) {
                $backendItem = $invoice->items()->create([]);
            } else {
                $receivedItemIds[] = $backendItem->id;
            }

            $backendItem->forceFill($regularItemData);
            $backendItem->save();

            $translations = array_get($frontendItem, 'translations', []);
            $backendItem->updateTranslations($translations);

            $variables = array_get($frontendItem, 'variables', []);
            $backendItem->variables()->delete();
            foreach($variables as $key => $value){
                $key = $key ?: '';
                $value = $value ?: '';
                $backendItem->variables()->create(compact('key', 'value'));
            }
        }

        /**
         * Remove items that were deleted
         */
        $deletableItemIds = [];
        foreach($itemIdsBefore as $itemIdBefore) {
            if(!in_array($itemIdBefore, $receivedItemIds)) {
                $deletableItemIds[] = $itemIdBefore;
            }
        }

        if($deletableItemIds) {
            $invoice->items()->whereIn('id', $deletableItemIds)->delete();
        }

        $invoice->load('items'); // Refresh
        $invoice->updateTotalSum();

        return $invoice;
    }

}
