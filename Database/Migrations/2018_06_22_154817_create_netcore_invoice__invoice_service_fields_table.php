<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreInvoiceInvoiceServiceFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Module::has('Product')) {
            return;
        }

        Schema::create('netcore_invoice__invoice_service_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_service_id');
            $table->string('key');
            $table->text('value');

            $table
                ->foreign('invoice_service_id', 'invoice::invoice_service_fields-service_foreign')
                ->references('id')
                ->on('netcore_invoice__invoice_services')
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
        if (!Module::has('Product')) {
            return;
        }

        Schema::dropIfExists('netcore_invoice__invoice_service_fields');
    }
}
