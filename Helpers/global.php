<?php

use Modules\Invoice\Repositories\InvoiceRepository;

if (!function_exists('invoice')) {
    /**
     * Get invoice helper.
     *
     * @return \Modules\Invoice\Repositories\InvoiceRepository
     */
    function invoice(): InvoiceRepository
    {
        return app(InvoiceRepository::class);
    }
}