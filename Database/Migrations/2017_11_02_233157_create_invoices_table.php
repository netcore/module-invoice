<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
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

            $table->string('payment_details')->nullable();
            $table->text('sender_data')->nullable();
            $table->text('receiver_data')->nullable();
            $table->text('data')->nullable();

            $table->enum('shipping_status', ['pending', 'shipped', 'received'])->default('pending');

            if (!Module::has('Payment')) {
                $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            } else {
                $table->unsignedInteger('payment_id')->nullable();
                $table->foreign('payment_id')->references('id')->on('netcore_payment__payments')->onDelete('restrict');
            }

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