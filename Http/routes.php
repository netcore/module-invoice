<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/invoices',
    'namespace'  => '\Modules\Invoice\Http\Controllers',
    'as'         => 'invoice::',
], function () {

    Route::get('/datatable-pagination', [
        'uses' => 'InvoiceController@datatablePagination',
        'as'   => 'datatable-pagination'
    ]);

    Route::get('/', [
        'uses' => 'InvoiceController@index',
        'as'   => 'index',
    ]);

    Route::get('/{invoice}', [
        'uses' => 'InvoiceController@show',
        'as'   => 'show'
    ]);

    Route::get('/create', [
        'uses' => 'InvoiceController@create',
        'as'   => 'create'
    ]);

    Route::post('/store', [
        'uses' => 'InvoiceController@store',
        'as'   => 'store'
    ]);

    Route::get('/edit/{invoice}', [
        'uses' => 'InvoiceController@edit',
        'as'   => 'edit'
    ]);

    Route::put('/update/{invoice}', [
        'uses' => 'InvoiceController@update',
        'as'   => 'update'
    ]);

    Route::get('/relation/pagination', [
        'uses' => 'InvoiceController@relationPagination',
        'as'   => 'relation-pagination'
    ]);
});