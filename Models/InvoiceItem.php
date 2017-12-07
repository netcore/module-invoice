<?php

namespace Modules\Invoice\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Invoice\Models\InvoiceItemVariable;
use Modules\Invoice\Translations\InvoiceItemTranslation;

/**
 * Modules\Invoice\Models\InvoiceItem
 *
 * @property int $id
 * @property int $invoice_id
 * @property float $price_with_vat
 * @property float $price_without_vat
 * @property int $quantity
 * @property-read float $total
 * @property-read \Modules\Invoice\Models\Invoice $invoice
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Invoice\Translations\InvoiceItemTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem listsTranslations($translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem notTranslatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem orWhereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem orWhereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem translated()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem translatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem wherePriceWithVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem wherePriceWithoutVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem whereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem whereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Invoice\Models\InvoiceItem withTranslation()
 * @mixin \Eloquent
 */
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
    protected $with = ['translations', 'variables'];

    /**
     * Register model events
     */
    public static function boot()
    {
        static::created(function(InvoiceItem $item) {
            $item->invoice->updateTotalSum();
        });

        static::updated(function(InvoiceItem $item) {
            $item->invoice->updateTotalSum();
        });

        parent::boot();
    }

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

    /**
     * Return relation with InvoiceItemVariable
     *
     * @return HasMany
     */
    public function variables(): HasMany
    {
        return $this->hasMany(InvoiceItemVariable::class);
    }

    /** -------------------- Accessors -------------------- */

    /**
     * Get the total with vat : price with vat multiplied by quantity
     *
     * @return float
     */
    public function getTotalWithVatAttribute(): float
    {
        return (float)number_format($this->price_with_vat * $this->quantity, 2, '.', '');
    }

    /**
     * Get the total with vat : price with vat multiplied by quantity
     *
     * @return float
     */
    public function getTotalWithoutVatAttribute(): float
    {
        return (float)number_format($this->price_without_vat * $this->quantity, 2, '.', '');
    }

    /** -------------------- Other methods -------------------- */

    /**
     * Create variables based on the passed array
     *
     * @param array $data
     * @return mixed
     */
    public function createVariables(array $data)
    {
        foreach ($data as $key => $value) {

            $this->variables()->create([
                'key'       =>  $key,
                'value'     =>  $value
            ]);

        }

        return $this->variables()->getResults();
    }

    /**
     * Return variable from variables table
     *
     * @param string $key
     * @return null|string
     */
    public function getVariable(string $key): ?string
    {
        $variables = $this->variables->pluck('value', 'key');

        return $variables[$key] ?? null;
    }

}