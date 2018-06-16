<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreInvoiceInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_invoice__invoice_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id')->index();

            $table->decimal('price_with_vat', 7, 2)->default(0);
            $table->decimal('price_without_vat', 7, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);

            $table->foreign('invoice_id', 'invoice_id_foreign')->references('id')->on('netcore_invoice__invoices')->onDelete('CASCADE');
        });

        // Items are translatable
        Schema::create('netcore_invoice__invoice_item_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_item_id')->index('invoice_item_id_index');
            $table->string('locale', 2)->index();

            $table->string('name');

            $table->unique(['invoice_item_id', 'locale'], 'item_locale_unique');
            $table->foreign('invoice_item_id', 'invoice_item_foreign')->references('id')->on('netcore_invoice__invoice_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('netcore_invoice__invoice_item_translations');
        Schema::dropIfExists('netcore_invoice__invoice_items');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
