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

            $table->string('payment_details')->nullable();
            $table->text('sender_data')->nullable();
            $table->text('receiver_data')->nullable();
            $table->text('data')->nullable();

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
