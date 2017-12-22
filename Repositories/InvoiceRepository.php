<?php

namespace Modules\Invoice\Repositories;

use Doctrine\DBAL\Types\ArrayType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Modules\Invoice\Exceptions\InvoiceBaseException;
use Modules\Invoice\Models\Invoice;
use Netcore\Translator\Helpers\TransHelper;

class InvoiceRepository
{
    /**
     * Enabled Invoice model relations.
     *
     * @var array
     */
    protected $enabledInvoiceRelations = [];

    /**
     * VAT amount.
     *
     * @var int
     */
    protected $vat;

    /**
     * Sender data.
     *
     * @var array
     */
    protected $senderData = [];

    /**
     * Receiver data.
     *
     * @var array
     */
    protected $receiverData = [];

    /**
     * Invoice items.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Invoice number.
     *
     * @var string
     */
    protected $invoiceNr;

    /**
     * Payment details.
     *
     * @var string
     */
    protected $paymentDetails;

    /**
     * Associated relations.
     *
     * @var array
     */
    protected $associatedRelations = [];

    /**
     * InvoiceRepository constructor.
     */
    public function __construct()
    {
        $this->vat = config('netcore.module-invoice.vat', 21);
        $this->senderData = config('netcore.module-invoice.sender', []);

        $this->enabledInvoiceRelations = collect(
            config('netcore.module-invoice.relations')
        )->where('enabled', true)->keyBy('name');

        $this->items = collect();
    }

    /**
     * Set relations fields - user_id/order_id etc.
     *
     * @param string $relationName
     * @param int $id
     */
    public function associateWithRelation(string $relationName, int $id)
    {
        $relation = array_get($this->enabledInvoiceRelations, $relationName);

        if (!$relation) {
            return $this;
        }

        $this->associatedRelations[array_get($relation, 'foreignKey')] = $id;

        return $this;
    }

    /**
     * Override invoice nr.
     *
     * @param string $invoiceNr
     */
    public function setInvoiceNr(string $invoiceNr)
    {
        $this->invoiceNr = $invoiceNr;

        return $this;
    }

    /**
     * Override VAT amount.
     *
     * @param int $vat
     * @return $this
     */
    public function setVat(int $vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Set invoice items.
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = collect($items);

        return $this;
    }

    /**
     * Override sender data.
     *
     * @param array $data
     * @return $this
     */
    public function setSender(array $data)
    {
        $this->senderData = $data;

        return $this;
    }

    /**
     * Override receiver data.
     *
     * @param array $data
     * @return $this
     */
    public function setReceiver(array $data)
    {
        $this->receiverData = $data;

        return $this;
    }

    /**
     * Merge receiver data.
     *
     * @param array $data
     * @return $this
     */
    public function mergeReceiver(array $data)
    {
        $this->receiverData = array_merge($this->receiverData, $data);

        return $this;
    }

    /**
     * Set user as invoice receiver
     *
     * @param Authenticatable $authenticatable
     * @throws InvoiceBaseException
     * @return $this
     */
    public function forUser(Authenticatable $authenticatable)
    {
        if (!$this->hasEnabledRelation('user')) {
            throw new InvoiceBaseException('User relation is not enabled.');
        }

        if (!method_exists($authenticatable, 'getInvoiceReceiverData')) {
            throw new InvoiceBaseException('Method [getInvoiceReceiverData] does not exist in ' . get_class($authenticatable) . ' class.');
        }

        $dataFromUser = $authenticatable->getInvoiceReceiverData();

        if (!is_array($dataFromUser)) {
            throw new InvoiceBaseException('Method [getInvoiceReceiverData] should return array or collection.');
        }

        $this->receiverData = array_merge($this->receiverData, $dataFromUser);

        $this->associateWithRelation('user', object_get($authenticatable, 'id'));

        return $this;
    }

    /**
     * Set payment details field.
     *
     * @param string $details
     * @return $this
     */
    public function setPaymentDetails(string $details)
    {
        $this->paymentDetails = $details;

        return $this;
    }

    /**
     * Create invoice
     *
     * @return Invoice
     */
    public function make(): Invoice
    {
        // Generate invoice nr. from id
        if (!$this->invoiceNr) {
            $lastOrder = Invoice::orderBy('id', 'desc')->first();
            $lastOrderId = object_get($lastOrder, 'id', 0);

            $nextId = $lastOrderId + 1;

            $invoiceNrPrefix = config('netcore.module-invoice.invoice_nr_prefix', 'INV');
            $padBy = (int)config('netcore.module-invoice.invoice_nr_padded_by', 6);
            $paddedId = str_pad($nextId, $padBy, '0', STR_PAD_LEFT);

            $this->invoiceNr = $invoiceNrPrefix . $paddedId;
        }

        $pricesGivenWithVat = config('netcore.module-invoice.prices_given_with_vat', true);
        $vatPercent = $this->vat * 0.01; // 0.21
        $vatPercentFull = 1 + $vatPercent; // 1.21

        $invoiceData = [
            'invoice_nr'        => $this->invoiceNr,
            'total_with_vat'    => 0,
            'total_without_vat' => 0,
            'vat'               => $this->vat,
            'sender_data'       => $this->senderData,
            'receiver_data'     => $this->receiverData,
            'payment_details'   => $this->paymentDetails,
        ];

        $invoice = Invoice::create(
            array_merge($invoiceData, $this->associatedRelations)
        );

        foreach ($this->items as $itemData) {
            $price = (float)array_get($itemData, 'price', 0);

            // Calculate price with and without VAT
            if ($pricesGivenWithVat) {
                $priceWithVat = $price;
                $priceWithoutVat = $price / $vatPercentFull;
            } else {
                $priceWithVat = $price * $vatPercentFull;
                $priceWithoutVat = $price;
            }

            $priceWithoutVat = round($priceWithoutVat, 2);
            $priceWithVat = round($priceWithVat, 2);

            $item = $invoice->items()->create([
                'price_with_vat'    => $priceWithVat,
                'price_without_vat' => $priceWithoutVat,
                'quantity'          => array_get($itemData, 'quantity', 1),
            ]);

            $item->createVariables(array_get($itemData, 'variables', []));

            $translations = [];

            // Same name given for all languages
            if ($name = array_get($itemData, 'name')) {
                foreach (TransHelper::getAllLanguages() as $language) {
                    $translations[$language->iso_code] = ['name' => $name];
                }
            } else {
                $translations = array_get($itemData, 'translations', []);
            }

            $item->storeTranslations($translations);
        }

        return $invoice;
    }

    /**
     * Check if relation is enabled
     *
     * @param string $name
     * @return bool
     */
    public function hasEnabledRelation(string $name): bool
    {
        static $relationsCollection;

        if (!$relationsCollection) {
            $relationsCollection = collect(
                (array)config('netcore.module-invoice.relations', [])
            );
        }

        return array_get($relationsCollection->where('name', $name)->first(), 'enabled');
    }

    /**
     * Get the total invoices count.
     *
     * @return int
     */
    public function totalCount()
    {
        return Invoice::count();
    }

    public function relationPagination(String $foreignKey, String $keyword)
    {

        $relations = config('netcore.module-invoice.relations');
        $currentRelation = collect($relations)->where('foreignKey', $foreignKey)->first();

        if (!$currentRelation) {
            return [];
        }

        $class = array_get($currentRelation, 'class');
        $table = app()->make($class)->getTable();
        $ajaxSelect = array_get($currentRelation, 'ajaxSelect', []);
        $translatable = array_get($ajaxSelect, 'translatable', []);
        $notTranslatable = array_get($ajaxSelect, 'notTranslatable', []);

        $query = app()->make($class);

        /**
         * not-translatable fields
         */
        if (count($notTranslatable) == 1) {
            $firstField = array_get($notTranslatable, 0);
            $query->where($firstField, $keyword);
        } elseif (count($notTranslatable) > 1) {
            $query = $query->where(function ($subq) use ($notTranslatable, $keyword) {
                foreach ($notTranslatable as $index => $field) {
                    if ($index == 0) {
                        $subq->where($field, 'LIKE', '%' . $keyword . '%');
                    } else {
                        $subq->orWhere($field, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });
        }

        /**
         * translatable fields
         */
        if ($translatable) {
            $query = $query->whereHas('translations', function ($subq) use ($translatable, $keyword) {
                foreach ($translatable as $index => $field) {
                    if ($index == 0) {
                        $subq->where($field, 'LIKE', '%' . $keyword . '%');
                    } else {
                        $subq->orWhere($field, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });
        }

        $items = $query->get()->map(function ($item) use ($foreignKey) {
            return [
                'id'   => $item->id,
                'text' => $this->labelForRelationItem($item, $foreignKey)
            ];
        });

        $items = [
            'items'       => $items,
            'total_count' => 1
        ];

        return $items;
    }

    /**
     * @param $item
     * @param $foreignKey
     * @return String
     */
    public function labelForRelationItem($item, $foreignKey): String
    {
        try {
            $relations = config('netcore.module-invoice.relations');
            $currentRelation = collect($relations)->where('foreignKey', $foreignKey)->first();

            $ajaxSelect = array_get($currentRelation, 'ajaxSelect', []);
            $translatable = array_get($ajaxSelect, 'translatable', []);
            $notTranslatable = array_get($ajaxSelect, 'notTranslatable', []);

            $textItems = [];

            foreach ($notTranslatable as $field) {
                $textItems[] = $item->$field;
            }

            foreach ($translatable as $field) {
                $textItems[] = $item->$field;
            }

            $result = join($textItems, ' ');
            return $result;
        } catch (\Throwable $e) {
            return '';
        }
    }
}