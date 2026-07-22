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
            'IT Department' => ['IT Support', 'System Administrator', 'Network Engineer', 'IT Manager'],
            'Human Resources' => ['HR Assistant', 'HR Generalist', 'Recruiter', 'HR Manager'],
            'Sales & Marketing' => ['Sales Associate', 'Marketing Coordinator', 'Account Executive', 'Sales Manager'],
            'Operations' => ['Operations Staff', 'Operations Supervisor', 'Operations Manager'],
            'Finance & Accounting' => ['Accounting Clerk', 'Accountant', 'Finance Analyst', 'Finance Manager'],
            'Logistics' => ['Warehouse Staff', 'Logistics Coordinator', 'Logistics Manager'],
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