<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyIdColumnToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('netcore_invoice__invoices', function (Blueprint $table) {

            $table->unsignedInteger('currency_id')
                  ->nullable()
                  ->after('id');

            $table->foreign('currency_id')
                  ->references('id')
                  ->on('netcore_subscription__currencies');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('netcore_invoice__invoices', function (Blueprint $table) {

            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');

        });
    }
}
