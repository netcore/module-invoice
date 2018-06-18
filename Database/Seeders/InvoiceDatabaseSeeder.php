<?php

namespace Modules\Invoice\Database\Seeders;

use Illuminate\Database\Seeder;

class InvoiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedAdminMenu();
    }

    /**
     * Seed admin menu.
     *
     * @return void
     */
    protected function seedAdminMenu(): void
    {
        menu()->seedItems([
            'leftAdminMenu' => [
                [
                    'name'            => 'Invoices',
                    'icon'            => 'fa-file-pdf-o',
                    'type'            => 'route',
                    'is_active'       => 1,
                    'value'           => 'invoice::index',
                    'active_resolver' => 'invoice::*',
                    'module'          => 'Invoice',
                    'parameters'      => json_encode([]),
                ],
            ],
        ]);
    }
}
