<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreInvoiceInvoiceItemVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_invoice__invoice_item_variables', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('invoice_item_id');
            $table->string('key')->index();
            $table->string('value');

            $table
                ->foreign('invoice_item_id')
                ->references('id')
                ->on('netcore_invoice__invoice_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_invoice__invoice_item_variables');
    }
}
