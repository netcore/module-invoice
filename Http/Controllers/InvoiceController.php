<?php

namespace Modules\Invoice\Http\Controllers;

use Exception;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;

use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Traits\InvoiceDatatableTrait;
use Modules\Invoice\Http\Requests\InvoiceRequest;
use Modules\Invoice\Repositories\InvoiceRepository;

class InvoiceController extends Controller
{
    use InvoiceDatatableTrait;

    /**
     * Display a listing of invoices.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $relations = config('netcore.module-invoice.relations');
        $relations = collect($relations)->where('enabled', true)->where('table.show', true);

        return view('invoice::admin.index', compact('relations'));
    }

    /**
     * Show/download invoice.
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Modules\Invoice\Exceptions\InvoiceBaseException
     */
    public function show(Invoice $invoice)
    {
        $isDownload = request()->has('download');

        $pdf = $invoice->getPDF();
        $filename = $invoice->invoice_nr . '.pdf';

        return $isDownload ? $pdf->download($filename) : $pdf->inline($filename);
    }

    /**
     * Display invoice create form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('invoice::admin.create', [
            'model'  => new Invoice(),
            'config' => [],
        ]);
    }

    /**
     * Store invoice in the database.
     *
     * @param InvoiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(InvoiceRequest $request): RedirectResponse
    {
        $invoice = new Invoice();

        $invoice = $invoice->storage()->update(
            $request->all()
        );

        return redirect()->route('invoice::edit', $invoice)->withSuccess('Invoice saved!');
    }

    /**
     * Display invoice edit form.
     *
     * @param Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function edit(Invoice $invoice): View
    {
        return view('invoice::admin.edit', [
            'model'  => $invoice,
            'config' => [],
        ]);
    }

    /**
     * Update invoice in the database.
     *
     * @param Invoice $invoice
     * @param InvoiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(Invoice $invoice, InvoiceRequest $request): RedirectResponse
    {
        $invoice->storage()->update(
            $request->all()
        );

        return redirect()->back()->withSuccess('Invoice saved!');
    }

    /**
     * Delete invoice.
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        try {
            $invoice->delete();
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Paginate relational fields.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function relationPagination(): JsonResponse
    {
        $itemsPerPage = 30;
        $keyword = request()->get('q') ?: '';
        $page = request()->get('page') ?: 1;
        $foreignKey = request()->get('foreignKey'); // e.g. currency_id or user_id

        $repo = new InvoiceRepository();
        $items = $repo->relationPagination($foreignKey, $keyword, $itemsPerPage, $page);

        return response()->json($items);
    }

    /**
     * Send invoice to client.
     *
     * @param \Modules\Invoice\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Invoice $invoice): RedirectResponse
    {
        $invoice->sendInvoiceToUser();
        $invoice->update(['is_sent' => true]);

        return back()->withSuccess('Invoice has been successfully sent!');
    }
}