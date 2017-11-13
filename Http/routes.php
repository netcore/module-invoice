<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/invoices',
    'namespace'  => 'Modules\Invoice\Http\Controllers',
    'as'         => 'invoice::',
], function () {

    Route::get('/', [
        'uses' => 'InvoiceController@index',
        'as'   => 'index',
    ]);

    Route::get('/datatable-pagination', [
        'uses' => 'InvoiceController@datatablePagination',
        'as'   => 'datatable-pagination'
    ]);

    Route::get('/make', function() {
        invoice()->forUser(auth()->user())->make();
    });

    Route::get('/test', function() {
        $i = \Modules\Invoice\Models\Invoice::first();

        dd($i->user()->first());
    });

});