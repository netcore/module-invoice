<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/invoices',
    'namespace'  => 'Modules\Invoice\Http\Controllers',
    'as'         => 'invoice::',
], function () {

    Route::get('/datatable-pagination', [
        'uses' => 'InvoiceController@datatablePagination',
        'as'   => 'datatable-pagination'
    ]);

    Route::get('/', [
        'uses' => 'InvoiceController@index',
        'as'   => 'x.index',
    ]);

    Route::get('/{invoice}', [
        'uses' => 'InvoiceController@show',
        'as'   => 'x.show'
    ]);

    Route::get('/create', [
        'uses' => 'InvoiceController@create',
        'as'   => 'x.create'
    ]);

    Route::get('/edit/{invoice}', [
        'uses' => 'InvoiceController@edit',
        'as'   => 'x.edit'
    ]);

    Route::put('/update/{invoice}', [
        'uses' => 'InvoiceController@update',
        'as'   => 'x.update'
    ]);

    Route::get('/relation/pagination', [
        'uses' => 'InvoiceController@relationPagination',
        'as'   => 'x.relation-pagination'
    ]);
});