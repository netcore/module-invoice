<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreInvoiceInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_invoice__invoices', function (Blueprint $table) {
            $table->increments('id');

            $table->string('invoice_nr')->index()->nullable();
            $table->decimal('total_with_vat', 7, 2)->default(0);
            $table->decimal('total_without_vat', 7, 2)->default(0);
            $table->unsignedInteger('vat')->nullable();

            $table->string('type')->default('invoice');
            $table->string('status')->default('new');
            $table->string('payment_method')->nullable();
            $table->string('payment_details')->nullable();

            if (!Module::has('Payment')) {
                $table->string('payment_status')->nullable()->default('unpaid');
            } else {
                $table->unsignedInteger('payment_id')->nullable();
                $table->foreign('payment_id')->references('id')->on('netcore_payment__payments')->onDelete('restrict');
            }

            $table->string('currency_symbol', 5)->nullable();
            $table->string('currency_code', 3)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_invoice__invoices');
    }
}