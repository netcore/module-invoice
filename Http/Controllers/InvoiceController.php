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
        return view('invoice::admin.index');
    }
}
