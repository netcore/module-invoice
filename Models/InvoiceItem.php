<?php

namespace Modules\Invoice\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Invoice\Translations\InvoiceItemTranslation;

class InvoiceItem extends Model
{
    use SyncTranslations, Translatable;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoice_items';

    /**
     * Mass assignable table fields.
     *
     * @var array
     */
    protected $fillable = [
        'price_with_vat',
        'price_without_vat',
        'quantity',
    ];

    /**
     * Disable timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Translation model.
     *
     * @var string
     */
    public $translationModel = InvoiceItemTranslation::class;

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name',
    ];

    /**
     * Eager-load relations.
     *
     * @var array
     */
    protected $with = ['translations'];

    /** -------------------- Relations -------------------- */

    /**
     * Invoice item belongs to invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /** -------------------- Accessors -------------------- */

    /**
     * Get the total : price with vat multiplied by quantity
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return (float)number_format($this->price_with_vat * $this->quantity, 2, '.', '');
    }
}