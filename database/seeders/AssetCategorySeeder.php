<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Desktop', 'prefix' => 'DSK'],
            ['name' => 'Laptop', 'prefix' => 'LAP'],
            ['name' => 'Mobile Phone', 'prefix' => 'MOB'],
            ['name' => 'Printer', 'prefix' => 'PRN'],
            ['name' => 'Router', 'prefix' => 'RTR'],
            ['name' => 'Switch', 'prefix' => 'SWT'],
            ['name' => 'Monitor', 'prefix' => 'MON'],
            ['name' => 'UPS', 'prefix' => 'UPS'],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
