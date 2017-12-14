## Module for creating invoices
This module was made for easy invoices creating.

## Features

- Translatable invoice items
- Invoice can be attached to user, order or any other related stuff
- Custom PDF templates

## Pre-installation

This module is part of Netcore CMS ecosystem and is only functional in a project that has following packages installed:

1. https://github.com/netcore/netcore
2. https://github.com/netcore/module-admin
3. https://github.com/netcore/module-translate

### Installation

 1. Require this package using composer
```bash
    composer require netcore/module-invoice
```

 2. Publish assets/configuration/migrations
```bash
    php artisan module:publish Invoice
    php artisan module:publish-config Invoice
    php artisan module:publish-migration Invoice
```

 3. Important - Configure relations before migrating
```text
    edit config/netcore/module-invoice.php file to enable/disable used relations
```

 4. Run the migrations and seeder
```bash 
    php artisan migrate
    php artisan module:seed Invoice
```

### Configuration

 - Configuration file is available at config/netcore/module-invoice.php

#### Relations

- Relations will be loaded from config
```php
    return [
        'relations' => [
            [
                'name'       => 'user', // relation name
                'type'       => 'belongsTo', // relation type
                'foreignKey' => 'user_id', // foreign key (user_id in invoices table in this case)
                'ownerKey'   => 'id', // owner key (id in related table in this case)
                'enabled'    => false, // is relation enabled? (it should be enable when migrating)
                'class'      => \App\User::class, // related model class
    
                // Datatable colum config
                'table' => [
                    'show' => true, // Show this column?
                    'name' => 'User', // Column name?
    
                    'searchable' => true, // Is column searchable?
                    'sortable'   => true, // Is column sortable?
                    'd_data'     => 'user', // Datatables data param
                    'd_name'     => 'user.first_name', // Datatables SQL field param
                    'modifier'   => 'fullName', // Accessor in model to format display format
                ],
            ],
        ...
    ];
```

#### User relation

- To use ->forUser() method, you should implement getInvoiceReceiverData() method in your User model
```php 
    /**
     * Get user data for invoices.
     *
     * @return array
     */
    public function getInvoiceReceiverData(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
        ];
    }
```

### Creating invoice

- To create invoice, you can use invoice() helper method
```php 
    $user = auth()->user();
    $items = [
        [
            'price' => 10.99,
            'name'  => 'Test item #1' // Name is equal for all languages 
        ],
        [
            'price' => 25.65,
            // Name is different for each language
            'translations' => [
                'en' => ['name' => 'First product.'],                    
                'ru' => ['name' => 'Первый товар..'],         
                'lv' => ['name' => 'Pirmā prece.'],
            ],
        ]
    ];

    $invoice = invoice()
        ->setItems($items)
        ->setPaymentDetails('VISA ending XXXX')
        
        ->forUser($user) // optional - set associated user (user relation should be enabled and configured)
        ->setInvoiceNr('MY123') // optional - set custom invoice nr.
        ->setVat(21) // optional - overrides vat specified in config
        ->setSender([ 'name' => 'My awesome company', ... ]) // optional - overrides sender data specified in config
        ->setReceiver([ 'first_name' => ..., 'last_name' => ... ]) // optional - overrides receiver data
        ->mergeReceiver([ 'some_additional_field' => ... ]) // optional - use if you need to add some extra receiver data

        ->make(); // build eveything up and returns Invoice instance
```
