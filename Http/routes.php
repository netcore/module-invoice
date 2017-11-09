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

    Route::get('/make', function () {
        return invoice()->setItems([
            [
                'price' => 199.21,
                'name'  => 'Test item 1',
            ],
            [
                'price' => 958.36,
                'name'  => 'Test item 2',
            ],
        ])->forUser(auth()->user())->make();
    });

    Route::get('/t', function () {
        app()->setLocale('en');

        $invoice = \Modules\Invoice\Models\Invoice::first();

        return $invoice->getPDF()->stream();
    });

});