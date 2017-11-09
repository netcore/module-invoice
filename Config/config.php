<?php


return [
    'name'                  => 'Invoice',

    /**
     * PDF settings
     */
    'pdf'                   => [
        'view' => 'invoice::pdf.example',
    ],

    /**
     * Sender data
     */
    'sender'                => [
        'company_name'        => 'Company Ltd.',
        'registration_number' => '0000000000000',
        'address'             => 'Country, Street No. 1',
        'zip_code'            => 'LV0000',
        'phone_number'        => '+000 0000000',
        'bank_name'           => 'Bank name',
        'bank_account'        => 'LV00BANK0000000000000',
        'email_address'       => 'support@example.com',
    ],

    /**
     * Zero-padded invoice nr.
     */
    'invoice_nr_padded_by'  => 6,

    /**
     * Invoice nr. prefix
     */
    'invoice_nr_prefix'     => 'INV',

    /**
     * ->setItems
     */
    'prices_given_with_vat' => false,
];
