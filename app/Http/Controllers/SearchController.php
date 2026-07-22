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
        $q->where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%");
    })
    ->limit(20)
    ->get();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'query' => $query,
                'assets' => $assets->map(fn ($a) => [
                    'id' => $a->id,
                    'asset_tag' => $a->asset_tag,
                    'name' => $a->name,
                    'serial_number' => $a->serial_number,
                    'category' => $a->category->name ?? null,
                    'department' => $a->department->name ?? null,
                    'assigned_to' => $a->assignedEmployee
                        ? trim($a->assignedEmployee->first_name.' '.$a->assignedEmployee->last_name)
                        : null,
                    'url' => route('assets.show', $a->id), // adjust to your actual route name
                ]),
                'employees' => $employees->map(fn ($e) => [
    'id' => $e->id,
    'name' => trim($e->first_name.' '.$e->last_name),
    'department' => $e->department->name ?? null,
    'url' => route('employees.show', $e->id),
]),
            ]);
        }

        return view('search.index', [
            'query' => $query,
            'assets' => $assets,
            'employees' => $employees,
        ]);
    }
}