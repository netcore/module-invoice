<?php

return [
    'name'     => 'Invoice',

    /**
     * PDF settings
     */
    'pdf'      => [
        'view' => 'invoice::pdf.master',
    ],

    /**
     * Set class and method to retrieve receiver data
     * Also can be set at runtime using ->setReceiverData(array $data) method
     */
    'receiver' => [
        'class'  => \App\User::class,
        'method' => 'getInvoiceData',
    ],

    /**
     * Sender data
     */
    'sender' => [
        'name'                => 'Company Ltd.',
        'registration_number' => '0000000000000',
        'address'             => 'Country, Street No. 1',
        'zip_code'            => 'LV0000',
        'phone_number'        => '+000 0000000',
        'bank_name'           => 'Bank name',
        'bank_account'        => 'LV00BANK0000000000000',
    ],
];
