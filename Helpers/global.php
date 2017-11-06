<?php

use Modules\Invoice\Repositories\InvoiceRepository;

if (! function_exists('invoice')) {
    function invoice() : InvoiceRepository {
        return app(InvoiceRepository::class);
    }
}