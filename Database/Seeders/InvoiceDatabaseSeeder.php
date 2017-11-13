<?php

namespace Modules\Invoice\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\Menu;

class InvoiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->seedAdminMenu();
    }

    /**
     * Seed admin menu
     */
    protected function seedAdminMenu()
    {
        // Seed admin menu
        $menus = [
            'leftAdminMenu' => [
                [
                    'name'            => 'Invoices',
                    'icon'            => 'fa-file-pdf-o',
                    'type'            => 'route',
                    'value'           => 'invoice::index',
                    'active_resolver' => 'invoice::*',
                    'module'          => 'Invoice',
                    'parameters'      => json_encode([]),
                ],
            ],
        ];

        foreach ($menus as $name => $items) {
            $menu = Menu::firstOrCreate([
                'name' => $name,
            ]);

            foreach ($items as $item) {
                $itemModel = $menu->items()->firstOrCreate($item);

                $itemModel->is_active = 1;
                $itemModel->save();
            }
        }
    }
}
