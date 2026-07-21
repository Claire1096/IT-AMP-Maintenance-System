<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Seeder;

class DepartmentLocationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Add your departments here
        $departments = [
            'IT Department',
            'Human Resources',
            'Sales & Marketing',
            'Operations',
            'Finance & Accounting',
            'Logistics',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name]);
        }

        // 2. Add your buildings here
        $buildings = [
            'Building A',
            'Building B',
            'Building C',
            'Building D',
        ];

        $buildingModels = [];
        foreach ($buildings as $name) {
            $buildingModels[$name] = Building::firstOrCreate(['name' => $name]);
        }

        // 3. Add your locations (rooms/floors) here, tied to a building above
        $locations = [
            ['building' => 'Building A', 'name' => 'IT Room'],
            ['building' => 'Building A', 'name' => '2nd Floor'],
            ['building' => 'Building B', 'name' => 'Sales Office'],
            ['building' => 'Building C', 'name' => 'Warehouse'],
            ['building' => 'Building D', 'name' => 'Finance Office'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate([
                'building_id' => $buildingModels[$loc['building']]->id,
                'name' => $loc['name'],
            ]);
        }
    }
}
