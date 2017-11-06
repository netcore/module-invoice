<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id')->index();
            $table->decimal('price_with_vat', 7, 2)->default(0);
            $table->decimal('price_without_vat', 7, 2)->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('netcore_invoice__invoices')->onDelete('CASCADE');
        });

        // Items are translatable
        Schema::create('netcore_invoice__invoice_items_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_item_id')->index();
            $table->string('locale', 2)->index();

            $table->string('name');

            $table->unique(['invoice_item_id', 'locale'], 'item_locale_unique');
            $table->foreign('invoice_item_id')->references('id')->on('netcore_invoice__invoice_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_invoice__invoice_item_translations');
        Schema::dropIfExists('netcore_invoice__invoice_items');
    }
}
