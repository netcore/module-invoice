<?php

namespace Modules\Invoice\Traits;

use Illuminate\Http\JsonResponse;
use Module;
use Modules\Invoice\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;

trait InvoiceDatatableTrait
{
    /**
     * Prepare data for datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatablePagination(): JsonResponse
    {
        $relations = config('netcore.module-invoice.relations', []);
        $relations = collect($relations)->where('enabled', true);

        $query = Invoice::query();

        if (Module::has('Payment')) {
            $query->with('payments');
        }

        $datatable = DataTables::of($query);

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

        $datatable->addColumn('payment', function ($row) {
            return view('invoice::admin.tds.payment', compact('row'))->render();
        });

        $datatable->addColumn('shipping', function ($row) {
            return ucfirst($row->shipping_status);
        });

        $datatable->addColumn('actions', function ($row) {
            return view('invoice::admin.tds.actions', compact('row'))->render();
        });

        $datatable->escapeColumns(false);

        return $datatable->make(true);
    }
}