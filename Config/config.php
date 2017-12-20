<?php

return [
    'name'      => 'Invoice',

    /**
     * Define invoice relations
     * Database structure will depend on enabled relations
     * If user/order relation is enabled, user_id/order field will be created
     * !!! Important: You should configure this before running migrations !!!
     */
    'relations' => [
        [
            'name'       => 'user',
            'type'       => 'belongsTo',
            'foreignKey' => 'user_id',
            'ownerKey'   => 'id',
            'enabled'    => false,
            'class'      => \App\User::class,

            'table' => [
                'show' => true,
                'name' => 'User',

                'searchable' => true,
                'sortable'   => true,
                'd_data'     => 'user',
                'd_name'     => 'user.first_name',
                'modifier'   => 'fullName',
            ],
        ],
        [
            'name'       => 'order',
            'type'       => 'belongsTo',
            'foreignKey' => 'order_id',
            'ownerKey'   => 'id',
            'enabled'    => false,
            'class'      => \Modules\Order\Models\Order::class,

            'table' => [
                'show' => true,
                'name' => 'Order ID',

                'searchable' => true,
                'sortable'   => true,
                'd_data'     => 'order',
                'd_name'     => 'order.id',
                'modifier'   => 'id',
            ],
        ],
    ],

    /**
     * PDF settings
     */
    'pdf'       => [
        'view' => 'invoice::pdf.example',
    ],

    /**
     * Sender data
     */
    'sender'    => [
        'company_name'        => 'Company Ltd.',
        'registration_number' => '0000000000000',
        'address'             => 'Country, Street No. 1',
        'zip_code'            => 'LV0000',
        'phone_number'        => '+000 0000000',
        'bank_name'           => 'Bank name',
        'bank_account'        => 'LV00BANK0000000000000',
        'email_address'       => 'support@example.com',
    ],

    'create_default_fields' => [
        'sender'   => [
            'company_name'    => '',
            'company_address' => '',
        ],
        'receiver' => [
            'full_name'  => '',
            'company'    => '',
            'vat_number' => '',
            'address'    => '',
            'city'       => '',
            'zip_code'   => ''
        ]
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
     * When creating - prices are passed with vat?
     */
    'prices_given_with_vat' => true,
];
