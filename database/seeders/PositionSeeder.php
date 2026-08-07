<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'IT DEPT' => ['IT Support', 'System Administrator', 'Network Engineer', 'IT Manager'],
            'HUMAN RESOURCE' => ['HR Assistant', 'HR Generalist', 'Recruiter', 'HR Manager'],
            'SALES AND MARKETING' => ['Sales Associate', 'Marketing Coordinator', 'Account Executive', 'Sales Manager'],
            'PRODUCTION' => ['Production Staff', 'Production Supervisor', 'Production Manager'],
            'FINANCE AND ACCOUNTING' => ['Accounting Clerk', 'Accountant', 'Finance Analyst', 'Finance Manager'],
            'LOGISTICS' => ['Warehouse Staff', 'Logistics Coordinator', 'Logistics Manager'],
            'EXECUTIVES' => ['Executive Assistant', 'Director', 'Vice President'],
        ];

        foreach ($map as $deptName => $titles) {
            $department = Department::where('name', $deptName)->first();
            if (! $department) continue;

            foreach ($titles as $title) {
                Position::firstOrCreate([
                    'department_id' => $department->id,
                    'title' => $title,
                ]);
            }
        }
    }
}
