<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::register('admin.invoice', function($breadcrumb) {
    $breadcrumb->parent('admin');
    $breadcrumb->push('Invoices', route('invoice::index'));
});