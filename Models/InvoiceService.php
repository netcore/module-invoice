<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoice\Contracts\ShippingHandlerContract;
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
        'service_type',
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

    /**
     * Invoice service has many additional fields.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields(): HasMany
    {
        return $this->hasMany(InvoiceServiceField::class);
    }

    /** -------------------- Helpers -------------------- */

    /**
     * Get service field.
     *
     * @param string $key
     * @return mixed
     */
    public function getField(string $key)
    {
        return optional($this->fields->where('key', $key)->first())->value;
    }

    /**
     * Get the instance of service handler.
     *
     * @return \Modules\Invoice\Contracts\ShippingHandlerContract
     */
    public function getServiceHandler(): ?ShippingHandlerContract
    {
        if (!$this->shippingOption || !$this->shippingOption->handler) {
            return null;
        }

        return app($this->shippingOption->handler);
    }
}