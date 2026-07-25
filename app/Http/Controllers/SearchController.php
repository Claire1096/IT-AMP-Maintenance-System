<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        $assets = collect();
        $employees = collect();

        if (trim($query) !== '') {
            $assets = Asset::query()
                ->with(['category', 'department', 'assignedEmployee'])
                ->where(function ($q) use ($query) {
                    $q->where('asset_tag', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhere('serial_number', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get();

            $employees = Employee::query()
                ->with('department')
                ->where(function ($q) use ($query) {
                    $q->where('employee_id', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get();
        }

        return view('search.index', [
            'query' => $query,
            'assets' => $assets,
            'employees' => $employees,
        ]);
    }
}

