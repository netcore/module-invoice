<?php

namespace Modules\Invoice\Translations;

use Illuminate\Database\Eloquent\Model;
use Modules\Invoice\Models\InvoiceItem;

/**
 * Modules\Invoice\Translations\InvoiceItemTranslation
 *
 * @property int $id
 * @property int $invoice_item_id
 * @property string $locale
 * @property string $name
 * @property-read \Modules\Invoice\Models\InvoiceItem $item
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Translations\InvoiceItemTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Translations\InvoiceItemTranslation whereInvoiceItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Translations\InvoiceItemTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Translations\InvoiceItemTranslation whereName($value)
 * @mixin \Eloquent
 */
class InvoiceItemTranslation extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoice_item_translations';

    /**
     * Mass assignable table fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'locale',
    ];

    /**
     * Disable timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Translation belongs to item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(InvoiceItem::class);
    }
}