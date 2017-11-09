<?php

namespace Modules\Invoice\Models;

use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Invoice\Exceptions\InvoiceBaseException;
use PDF;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoices';

    /**
     * Mass assignable table fields.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'invoice_nr',
        'total_with_vat',
        'total_without_vat',
        'sender_data',
        'receiver_data',
        'data',
        'vat',
    ];

    /**
     * Enable timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Value casts
     *
     * @var array
     */
    protected $casts = [
        'sender_data'   => 'array',
        'receiver_data' => 'array',
        'data'          => 'array',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Invoice has many items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /** -------------------- Other methods -------------------- */

    /**
     * Get PDF wrapper
     *
     * @return PdfWrapper
     * @throws InvoiceBaseException
     */
    public function getPDF() : PdfWrapper
    {
        $view = config('netcore.module-invoice.pdf.view');

        if (! $view || ! view()->exists($view)) {
            throw new InvoiceBaseException('PDF view is not set.');
        }

        // ---------------------------------------------------------------
        // Merge config on the fly
        // Global config merge can affect other modules using this package
        // ---------------------------------------------------------------

        if (config()->has('netcore.module-invoice-snappy')) {
            $originalConfig = config('snappy');
            $overrideConfig = config('netcore.module-invoice-snappy');

            config()->set('snappy', array_merge($originalConfig, $overrideConfig));
        }

        return PDF::loadView($view, ['invoice' => $this]);
    }

    /**
     * Get sender data property
     *
     * @param string $key
     * @param string $fallback
     * @return mixed
     */
    public function getSenderParam(string $key, $fallback = '')
    {
        return array_get($this->sender_data, $key, $fallback);
    }

    /**
     * Get receiver data property
     *
     * @param string $key
     * @param string $fallback
     * @return mixed
     */
    public function getReceiverParam(string $key, $fallback = '')
    {
        return array_get($this->receiver_data, $key, $fallback);
    }
}