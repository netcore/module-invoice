<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Modules\Product\Models\ShippingOption;
use Modules\Product\Models\ShippingOptionLocation;

class InvoiceService extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_invoice__invoice_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipping_option_id',
        'shipping_option_location_id',
        'is_sent_to_service',
        'service_side_id',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Invoice service belongs to invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Invoice service belongs to the shipping option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingOption(): BelongsTo
    {
        return $this->belongsTo(ShippingOption::class);
    }

    /**
     * Invoice service belongs to the shipping option location.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingOptionLocation(): BelongsTo
    {
        return $this->belongsTo(ShippingOptionLocation::class);
    }
}