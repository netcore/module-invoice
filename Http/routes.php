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

    Route::get('/{invoice}', [
        'uses' => 'InvoiceController@show',
        'as'   => 'show'
    ]);

});