<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreInvoiceInvoiceServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Module::has('Product')) {
            return;
        }

        Schema::create('netcore_invoice__invoice_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('shipping_option_id');
            $table->unsignedInteger('shipping_option_location_id')->nullable();
            $table->string('service_type')->nullable();
            $table->boolean('is_sent_to_service')->default(false);
            $table->string('service_side_id')->nullable();
            $table->timestamps();

            $invoiceForeign = 'invoice::invoice_services-invoice_foreign';
            $shippingForeign = 'invoice::invoice_services-shipping_foreign';
            $shippingOptionForeign = 'invoice::invoice_services-shipping_option_foreign';

            $table->foreign('invoice_id',
                $invoiceForeign)->references('id')->on('netcore_invoice__invoices')->onDelete('cascade');

            $table->foreign('shipping_option_id',
                $shippingForeign)->references('id')->on('netcore_product__shipping_options')->onDelete('restrict');
            
            $table->foreign('shipping_option_location_id',
                $shippingOptionForeign)->references('id')->on('netcore_product__shipping_option_locations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Module::has('Product')) {
            return;
        }

        Schema::dropIfExists('netcore_invoice__invoice_services');
    }
}
