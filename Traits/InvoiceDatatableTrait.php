<?php

namespace Modules\Invoice\Traits;

use Modules\Invoice\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;

trait InvoiceDatatableTrait
{
    public function datatablePagination()
    {
        $relations = config('netcore.module-invoice.relations', []);
        $relations = collect($relations)->where('enabled', true);

        $datatable = DataTables::of(
            Invoice::query()
        );

        // Modify relation display format
        foreach ($relations as $relation) {
            $table = array_get($relation, 'table');

            if (!$table || !$table['show']) {
                continue;
            }

            $datatable->editColumn($relation['name'], function($row) use($table, $relation) {
                return $row->{$relation['name']}->{$table['modifier']};
            });
        }

        $datatable->addColumn('actions', function ($row) {
            return view('invoice::admin._actions', compact('row'))->render();
        });

        $datatable->rawColumns([
            'actions',
        ]);

        return $datatable->make(true);
    }
}