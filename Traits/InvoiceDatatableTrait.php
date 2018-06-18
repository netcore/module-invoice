<?php

namespace Modules\Invoice\Traits;

use Module;
use Illuminate\Http\JsonResponse;
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

        $datatable->editColumn('status', function (Invoice $invoice) {
            return array_get(Invoice::$statuses, $invoice->status, 'Unknown status');
        });

        $datatable->addColumn('payment', function ($row) {
            return view('invoice::admin.tds.payment', compact('row'))->render();
        });

        $datatable->addColumn('actions', function ($row) {
            return view('invoice::admin.tds.actions', compact('row'))->render();
        });

        $datatable->escapeColumns(false);

        return $datatable->make(true);
    }
}