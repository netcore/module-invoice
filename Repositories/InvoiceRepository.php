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
     * VAT amount
     *
     * @var int
     */
    protected $vat;

    /**
     * Sender data
     *
     * @var array
     */
    protected $senderData = [];

    /**
     * Receiver data
     *
     * @var array
     */
    protected $receiverData = [];

    /**
     * Invoice items
     *
     * @var Collection
     */
    protected $items;

    /**
     * @var int|null
     */
    protected $user_id;

    /**
     * @var int|null
     */
    protected $order_id;

    /**
     * @var string
     */
    protected $invoice_nr;

    /**
     * InvoiceRepository constructor.
     */
    public function __construct()
    {
        $this->vat = config('netcore.module-invoice.vat', 21);
        $this->senderData = config('netcore.module-invoice.sender', []);

        $this->items = collect();
    }

    /**
     * Set order id
     *
     * @param int $id
     * @return $this
     */
    public function setOrderId(int $id)
    {
        $this->order_id = $id;

        return $this;
    }

    /**
     * Override invoice nr.
     *
     * @param string $invoice_nr
     */
    public function setInvoiceNr(string $invoice_nr)
    {
        $this->invoice_nr = $invoice_nr;
    }

    /**
     * Override VAT amount
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
     * Set invoice items
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
     * Override sender data
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
     * Override receiver data
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
     * Merge receiver data
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
        if (!method_exists($authenticatable, 'getInvoiceReceiverData')) {
            throw new InvoiceBaseException('Method [getInvoiceReceiverData] does not exist in ' . get_class($authenticatable) . ' class.');
        }

        $dataFromUser = $authenticatable->getInvoiceReceiverData();

        if (!is_array($dataFromUser)) {
            throw new InvoiceBaseException('Method [getInvoiceReceiverData] should return array or collection.');
        }

        $this->receiverData = array_merge($this->receiverData, $dataFromUser);
        $this->user_id = object_get($authenticatable, 'id');

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
        if (! $this->invoice_nr) {
            $lastOrder = Invoice::orderBy('id', 'desc')->first();
            $lastOrderId = object_get($lastOrder, 'id', 0);

            $nextId = $lastOrderId + 1;

            $invoiceNrPrefix = config('netcore.module-invoice.invoice_nr_prefix', 'INV');
            $padBy = (int)config('netcore.module-invoice.invoice_nr_padded_by', 6);
            $paddedId = str_pad($nextId, $padBy, '0', STR_PAD_LEFT);

            $this->invoice_nr = $invoiceNrPrefix . $paddedId;
        }

        $pricesGivenWithVat = config('netcore.module-invoice.prices_given_with_vat', true);
        $vatPercent = $this->vat * 0.01; // 0.21
        $vatPercentFull = 1 + $vatPercent; // 1.21

        // Count total sum of items
        $totalSum = $this->items->sum('price');

        // Calculate prices with and without VAT
        if ($pricesGivenWithVat) {
            $totalWithVat = $totalSum;
            $totalWithoutVat = $totalSum - ($totalSum / $vatPercentFull);
        } else {
            $totalWithVat = $totalSum * $vatPercentFull;
            $totalWithoutVat = $totalSum;
        }

        $invoice = Invoice::create([
            'user_id'           => $this->user_id,
            //'order_id'          => $this->order_id,
            'invoice_nr'        => $this->invoice_nr,
            'total_with_vat'    => $totalWithVat,
            'total_without_vat' => $totalWithoutVat,
            'vat'               => $this->vat,
            'sender_data'       => $this->senderData,
            'receiver_data'     => $this->receiverData,
        ]);

        foreach ($this->items as $itemData) {
            $price = (float)array_get($itemData, 'price', 0);

            // Calculate price with and without VAT
            if ($pricesGivenWithVat) {
                $priceWithVat = $price;
                $priceWithoutVat = $price - ($price / $vatPercentFull);
            } else {
                $priceWithVat = $price * $vatPercentFull;
                $priceWithoutVat = $price;
            }

            $item = $invoice->items()->create([
                'price_with_vat'    => $priceWithVat,
                'price_without_vat' => $priceWithoutVat,
            ]);

            $translations = [];

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
}