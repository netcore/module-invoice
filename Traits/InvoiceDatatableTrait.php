<?php

namespace Modules\Invoice\Traits;

use Modules\Invoice\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;

trait InvoiceDatatableTrait
{
    public function datatablePagination()
    {
        $invoices = Invoice::query();

        return DataTables::of($invoices)->make(true);
    }
}