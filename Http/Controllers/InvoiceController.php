<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Invoice\Models\Invoice;
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

    /**
     * Show/download invoice
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Invoice $invoice)
    {
        $isDownload = request()->has('download');

        $pdf = $invoice->getPDF();
        $filename = $invoice->invoice_nr . '.pdf';

        return $isDownload ? $pdf->download($filename) : $pdf->inline($filename);
    }
}
