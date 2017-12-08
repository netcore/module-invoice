<?php

namespace Modules\Invoice\Repositories;

use Doctrine\DBAL\Types\ArrayType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Modules\Invoice\Exceptions\InvoiceBaseException;
use Modules\Invoice\Models\Invoice;
use Modules\Subscription\Models\Currency;
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
     * Currency
     *
     * @var Currency
     */
    protected $currency;

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
    public function
    associateWithRelation(string $relationName, int $id)
    {
        $relation = array_get($this->enabledInvoiceRelations, $relationName);

        if (!$relation) {
            return;
        }

        $this->associatedRelations[array_get($relation, 'foreignKey')] = $id;
    }

    /**
     * Override invoice nr.
     *
     * @param string $invoiceNr
     */
    public function setInvoiceNr(string $invoiceNr)
    {
        $this->invoiceNr = $invoiceNr;
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
     * Set currency
     *
     * @param Currency $currency
     * @return InvoiceRepository
     */
    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

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
            'currency_id'       => $this->currency->id ?? null,
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

            $item->createVariables( array_get($itemData, 'variables', []) );

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
}