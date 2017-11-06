<?php

$groupParams = [
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/invoices',
    'namespace'  => 'Modules\Invoice\Http\Controllers',
    'as'         => 'invoice::',
];

Route::group($groupParams, function () {

    Route::get('/', [
        'uses' => 'InvoiceController@index',
        'as'   => 'index',
    ]);

    Route::get('/t', function() {
        invoice()->make(

        );
    });

});