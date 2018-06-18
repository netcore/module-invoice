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

    Route::get('/create', [
        'uses' => 'InvoiceController@create',
        'as'   => 'create'
    ]);

    Route::get('/{invoice}', [
        'uses' => 'InvoiceController@show',
        'as'   => 'show'
    ]);

    Route::post('/store', [
        'uses' => 'InvoiceController@store',
        'as'   => 'store'
    ]);

    Route::get('/edit/{invoice}', [
        'uses' => 'InvoiceController@edit',
        'as'   => 'edit'
    ]);

    Route::get('/send/{invoice}', [
        'uses' => 'InvoiceController@send',
        'as'   => 'send'
    ]);

    Route::put('/update/{invoice}', [
        'uses' => 'InvoiceController@update',
        'as'   => 'update'
    ]);

    Route::get('/relation/pagination', [
        'uses' => 'InvoiceController@relationPagination',
        'as'   => 'relation-pagination'
    ]);

    Route::delete('/destroy/{invoice}', [
        'uses' => 'InvoiceController@destroy',
        'as'   => 'destroy'
    ]);
});