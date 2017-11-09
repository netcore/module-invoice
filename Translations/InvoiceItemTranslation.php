<?php

namespace Modules\Invoice\Translations;

use Illuminate\Database\Eloquent\Model;
use Modules\Invoice\Models\InvoiceItem;

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