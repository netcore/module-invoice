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

    /**
     * @param Invoice $invoice
     * @return mixed
     */
    public function edit(Invoice $invoice)
    {
        $relations = config('netcore.module-invoice.relations');
        $relations = collect($relations)->where('enabled', true)->where('table.show', true);

        return view('invoice::admin.edit', [
            'model'  => $invoice,
            'config' => []
        ]);
    }

    /**
     * @return array
     */
    public function relationPagination()
    {
        $itemsPerPage = 30;
        $keyword = request()->get('q', '');
        $page = request()->get('page', 1);
        $foreignKey = request()->get('foreignKey'); // e.g. currency_id or user_id

        $relations = config('netcore.module-invoice.relations');
        $currentRelation = collect($relations)->where('foreignKey', $foreignKey)->first();

        if (!$currentRelation) {
            abort(404);
        }

        $class = array_get($currentRelation, 'class');
        $table = app()->make($class)->getTable();
        $ajaxSelect = array_get($currentRelation, 'ajaxSelect', []);
        $translatable = array_get($ajaxSelect, 'translatable', []);
        $notTranslatable = array_get($ajaxSelect, 'notTranslatable', []);

        $query = app()->make($class);

        /**
         * not-translatable fields
         */
        if (count($notTranslatable) == 1) {
            $firstField = array_get($notTranslatable, 0);
            $query->where($firstField, $keyword);
        } elseif (count($notTranslatable) > 1) {
            $query = $query->where(function ($subq) use ($notTranslatable, $keyword) {
                foreach ($notTranslatable as $index => $field) {
                    if ($index == 0) {
                        $subq->where($field, 'LIKE', '%'.$keyword.'%');
                    } else {
                        $subq->orWhere($field, 'LIKE', '%'.$keyword.'%');
                    }
                }
            });
        }

        /**
         * translatable fields
         */
        if ($translatable) {
            $query = $query->whereHas('translations', function ($subq) use ($translatable, $keyword) {
                foreach ($translatable as $index => $field) {
                    if ($index == 0) {
                        $subq->where($field, 'LIKE', '%'.$keyword.'%');
                    } else {
                        $subq->orWhere($field, 'LIKE', '%'.$keyword.'%');
                    }
                }
            });
        }

        $items = $query->get()->map(function($item) use ($notTranslatable, $translatable) {

            $textItems = [];

            foreach($notTranslatable as $field) {
                $textItems[] = $item->$field;
            }

            foreach($translatable as $field) {
                $textItems[] = $item->$field;
            }

            return [
                'id' => $item->id,
                'text' => join($textItems, ' ')
            ];
        });

        $items = [
            'items'       => $items,
            'total_count' => 1
        ];

        return $items;
    }
}
