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

            $this->setRelatedFields($table);

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

            $this->setForeignKeys($table);
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

    /**
     * Set related fields.
     *
     * @param Blueprint $table
     */
    private function setRelatedFields(Blueprint &$table)
    {
        $relations = config('netcore.module-invoice.relations', []);

        foreach ($relations as $relation) {
            if (! array_get($relation, 'enabled')) {
                continue;
            }

            $foreignKey = array_get($relation, 'foreignKey');
            $table->unsignedInteger($foreignKey)->index()->nullable();
        }
    }

    /**
     * Set foreign keys
     *
     * @param Blueprint $table
     */
    private function setForeignKeys(Blueprint &$table)
    {
        $relations = config('netcore.module-invoice.relations', []);

        foreach ($relations as $relation) {
            if (!array_get($relation, 'enabled')) {
                continue;
            }

            $foreignKey = array_get($relation, 'foreignKey');
            $ownerKey = array_get($relation, 'ownerKey');

            $className = array_get($relation, 'class');
            $tableName = class_exists($className) ? (new $className)->getTable() : null;

            $onDelete = array_get($relation, 'onDelete', 'CASCADE');

            if (!$tableName || !$foreignKey || !$ownerKey) {
                continue;
            }

            $table->foreign($foreignKey)->references($ownerKey)->on($tableName)->onDelete($onDelete);
        }
    }
}
