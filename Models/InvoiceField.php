<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modules\Invoice\Models\InvoiceField
 *
 * @property int $invoice_id
 * @property string $type
 * @property string $key
 * @property string $value
 * @property-read \Modules\Invoice\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceField whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceField whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceField whereValue($value)
 * @mixin \Eloquent
 */
class InvoiceField extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoice_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'type',
        'value',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /** -------------------- Relations -------------------- */

    /**
     * Invoice field belongs to the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}