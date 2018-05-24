<?php

namespace Modules\Invoice\Models;

use PDF;
use Barryvdh\Snappy\PdfWrapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Modules\Crud\Traits\CRUDModel;
use Modules\Payment\Modules\Payment;
use Modules\Invoice\PassThroughs\Invoice\Storage;
use Modules\Invoice\Exceptions\InvoiceBaseException;

/**
 * Modules\Invoice\Models\Invoice
 *
 * @property int $id
 * @property string|null $invoice_nr
 * @property float $total_with_vat
 * @property float $total_without_vat
 * @property int|null $vat
 * @property string|null $payment_details
 * @property array $sender_data
 * @property array $receiver_data
 * @property array $data
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Invoice\Models\InvoiceItem[] $items
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Modules\Invoice\Models\Invoice onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereInvoiceNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereReceiverData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereSenderData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereTotalWithVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereTotalWithoutVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\Invoice whereVat($value)
 * @method static \Illuminate\Database\Query\Builder|\Modules\Invoice\Models\Invoice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Modules\Invoice\Models\Invoice withoutTrashed()
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use SoftDeletes;
    use CRUDModel;

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
        'invoice_nr',
        'total_with_vat',
        'total_without_vat',
        'payment_method',
        'payment_details',
        'sender_data',
        'receiver_data',
        'data',
        'vat',
        'type',
        'currency_code',
        'currency_symbol',
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

    /**
     * Invoice constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        static $relations;

        if (!$relations) {
            $relations = config('netcore.module-invoice.relations');
            $relations = collect($relations)->where('enabled', true);
        }

        // Eager load registered relations
        $relations->each(function ($relation) {
            $this->with[] = $relation['name'];
            $this->fillable[] = $relation['foreignKey'];
        });

        parent::__construct($attributes);
    }

    /**
     * Dynamic method call.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        static $relations;

        if (!$relations) {
            $relations = config('netcore.module-invoice.relations');
            $relations = collect($relations)->where('enabled', true);
        }

        if ($relation = $relations->where('name', $method)->first()) {
            return $this->{$relation['type']}($relation['class'], $relation['foreignKey'], $relation['ownerKey']);
        }

        return parent::__call($method, $parameters);
    }

    /** -------------------- Relations -------------------- */

    /**
     * Invoice has many items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Invoice has many payments
     *
     * TODO: this should be refactored in next major release
     * (invoice should normally have one payment only)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** -------------------- PassThrough -------------------- */

    /**
     *
     * @return Storage
     */
    public function storage()
    {
        return new Storage($this);
    }

    /** -------------------- Other methods -------------------- */

    /**
     * Get PDF wrapper
     *
     * @return PdfWrapper|PDF
     * @throws InvoiceBaseException
     */
    public function getPDF()
    {
        $view = config('netcore.module-invoice.pdf.view');

        if (!$view || !view()->exists($view)) {
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

    /**
     * Update invoice total sum
     *
     * @return void
     */
    public function updateTotalSum(): void
    {
        $this->update([
            'total_without_vat' => $this->items->sum('total_without_vat'),
            'total_with_vat'    => $this->items->sum('total_with_vat'),
        ]);
    }

    /**
     * Return how much VAT is
     *
     * @return float
     */
    public function getTotalVatAttribute(): float
    {
        return (float)number_format($this->total_with_vat - $this->total_without_vat, 2, '.', '');
    }
}
