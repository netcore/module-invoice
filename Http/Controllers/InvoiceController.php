<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Invoice\Traits\InvoiceDatatableTrait;

class InvoiceController extends Controller
{
    use InvoiceDatatableTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $relations = config('netcore.module-invoice.relations');
        $relations = collect($relations)->where('enabled', true)->where('table.show', true);

        return view('invoice::admin.index', compact('relations'));
    }
}
