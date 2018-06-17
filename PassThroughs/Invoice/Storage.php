<?php

namespace Modules\Invoice\PassThroughs\Invoice;

use Illuminate\Support\Facades\DB;
use Module;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\PassThroughs\PassThrough;
use Netcore\Translator\Helpers\TransHelper;

class Storage extends PassThrough
{
    /**
     * Invoice model instance.
     *
     * @var Invoice
     */
    private $invoice;

    /**
     * Available application languages.
     *
     * @var Collection
     */
    private $languages;

    /**
     * Storage constructor.
     *
     * @param Invoice $invoice
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     * Update invoice.
     *
     * @param array $requestData
     * @return Invoice
     * @throws \Throwable
     */
    public function update(Array $requestData): Invoice
    {
        $invoice = DB::transaction(function () use ($requestData) {
            return $this->transaction($requestData);
        });

        return $invoice;
    }

    /**
     * DB transaction callback.
     *
     * @param array $requestData
     * @return Invoice
     */
    private function transaction(Array $requestData): Invoice
    {
        $invoice = $this->invoice;

        if (!Module::has('Payment')) {
            $requestData['payment_method'] = data_get($requestData, 'payment.method');
            $requestData['payment_status'] = data_get($requestData, 'payment.state');
        }

        /**
         * Regular data
         */
        $invoice->forceFill(array_only($requestData, [
            'vat',
            'status',
            'invoice_nr',
            'payment_details',
            'payment_status',
            'payment_method',
        ]));

        $invoice->fields()->delete();

        $sender = data_get($requestData, 'sender_data', []);
        $receiver = data_get($requestData, 'receiver_data', []);

        // Store invoice fields.
        $fieldsToInsert = [];

        foreach (collect(compact('sender', 'receiver')) as $type => $items) {
            foreach ($items as $key => $value) {
                $fieldsToInsert[] = compact('type', 'key', 'value');
            }
        }

        $invoice->fields()->createMany($fieldsToInsert);

        /**
         * Payment must be retrieved before updating relations
         * Otherwise double payments will be created
         */
        if (Module::has('Payment')) {
            if ($invoice->payments->count()) {
                // Update existing payment if possible
                $backendPayment = $invoice->payments()->firstOrCreate([
                    'user_id' => $invoice->user_id,
                ]);
            } else {
                // Create a brand new payment
                $backendPayment = $invoice->payments()->create([
                    'user_id' => array_get($requestData, 'user_id'),
                ]);
            }
        } else {
            $invoice->payment_status = data_get($requestData, 'payment.state');
        }

        /**
         * Relations
         */
        $relations = config('netcore.module-invoice.relations');
        $belongsTo = collect($relations)->where('type', 'belongsTo');

        foreach ($belongsTo as $relation) {
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

        foreach ($frontendItems as $frontendId => $frontendItem) {
            $regularItemData = array_only($frontendItem, [
                'price_without_vat',
                'price_with_vat',
                'quantity',
            ]);

            $backendItem = $backendItems->where('id', $frontendId)->first();

            if (!$backendItem) {
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

            foreach ($variables as $key => $value) {
                $key = $key ?: '';
                $value = $value ?: '';
                $backendItem->variables()->create(compact('key', 'value'));
            }
        }

        /**
         * Remove items that were deleted
         */
        $deletableItemIds = [];

        foreach ($itemIdsBefore as $itemIdBefore) {
            if (!in_array($itemIdBefore, $receivedItemIds)) {
                $deletableItemIds[] = $itemIdBefore;
            }
        }

        if ($deletableItemIds) {
            $invoice->items()->whereIn('id', $deletableItemIds)->delete();
        }

        $invoice->load('items'); // Refresh
        $invoice->updateTotalSum();

        /**
         * Payment
         */
        if (isset($backendPayment)) {
            $frontendPayment = array_get($requestData, 'payment', []);

            $backendPayment->forceFill([
                'invoice_id' => $invoice->id,
                'user_id'    => $invoice->user_id,
                'state'      => array_get($frontendPayment, 'state'),
                'method'     => array_get($frontendPayment, 'method'),
                'amount'     => $invoice->total_with_vat,
            ]);

            $backendPayment->save();
        }

        return $invoice;
    }

}
