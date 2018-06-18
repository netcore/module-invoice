<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Invoice\Translations\InvoiceItemVariableTranslation;

/**
 * Modules\Invoice\Models\InvoiceItemVariable
 *
 * @property int $id
 * @property int $invoice_item_id
 * @property string $key
 * @property string $value
 * @property-read \Modules\Invoice\Models\InvoiceItem $item
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItemVariable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItemVariable whereInvoiceItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItemVariable whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItemVariable whereValue($value)
 * @mixin \Eloquent
 */
class InvoiceItemVariable extends Model
{

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoice_item_variables';

    /**
     * Mass assignable table fields.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'invoice_item_id'
    ];

    /**
     * Disable timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * return a relation with InvoiceItem
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

}
