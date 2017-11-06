<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
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
    protected $fillable = [];

    /**
     * Enable timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

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

    /**
     * Invoice has many details
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}