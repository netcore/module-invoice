<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Invoice\Http\Requests\InvoiceRequest;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Repositories\InvoiceRepository;
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

    /**
     * @return mixed
     */
    public function create()
    {
        return view('invoice::admin.create', [
            'model'  => new Invoice(),
            'config' => []
        ]);
    }

    /**
     * @param InvoiceRequest $request
     * @return mixed
     */
    public function store(InvoiceRequest $request)
    {
        $requestData = $request->all();

        $invoice = new Invoice();
        $invoice = $invoice->storage()->update($requestData);

        return redirect()->route('invoice::edit', $invoice)->withSuccess('Invoice saved!');
    }

    /**
     * @param Invoice $invoice
     * @return mixed
     */
    public function edit(Invoice $invoice)
    {
        return view('invoice::admin.edit', [
            'model'  => $invoice,
            'config' => []
        ]);
    }

    /**
     * @param Invoice $invoice
     * @param InvoiceRequest $request
     * @return mixed
     */
    public function update(Invoice $invoice, InvoiceRequest $request)
    {
        $requestData = $request->all();
        $invoice->storage()->update($requestData);

        return redirect()->back()->withSuccess('Invoice saved!');
    }

    /**
     * @param Invoice $invoice
     * @return array
     * @throws \Exception
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return [
            'success' => true
        ];
    }

    /**
     * @return array
     */
    public function relationPagination()
    {
        $itemsPerPage = 30;
        $keyword = request()->get('q') ?: '';
        $page = request()->get('page') ?: 1;
        $foreignKey = request()->get('foreignKey'); // e.g. currency_id or user_id

        $repo = new InvoiceRepository();
        $items = $repo->relationPagination($foreignKey, $keyword, $itemsPerPage, $page);

        return $items;
    }
}
