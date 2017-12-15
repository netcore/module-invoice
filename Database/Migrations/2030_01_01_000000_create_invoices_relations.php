<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('netcore_invoice__invoices', function (Blueprint $table) {

            $this->setRelatedFields($table);
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
        Schema::table('netcore_invoice__invoices', function (Blueprint $table) {

            $this->dropForeignKeys($table);
            $this->dropRelatedFields($table);

        });
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
     * Drop related fields.
     *
     * @param Blueprint $table
     */
    private function dropRelatedFields(Blueprint &$table)
    {
        $relations = config('netcore.module-invoice.relations', []);

        foreach ($relations as $relation) {
            if (! array_get($relation, 'enabled')) {
                continue;
            }

            $foreignKey = array_get($relation, 'foreignKey');
            $table->dropColumn($foreignKey);
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

    /**
     * Drop foreign keys
     *
     * @param Blueprint $table
     */
    private function dropForeignKeys(Blueprint &$table)
    {
        $relations = config('netcore.module-invoice.relations', []);

        foreach ($relations as $relation) {
            if (!array_get($relation, 'enabled')) {
                continue;
            }

            $foreignKey = array_get($relation, 'foreignKey');

            $table->dropForeign([$foreignKey]);
        }
    }
}
