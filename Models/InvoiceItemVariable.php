<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Invoice\Translations\InvoiceItemVariableTranslation;

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
