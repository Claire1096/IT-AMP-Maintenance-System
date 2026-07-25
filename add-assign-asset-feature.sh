#!/bin/bash
set -e
mkdir -p app/Http/Controllers resources/views/employees

echo 'Writing app/Http/Controllers/EmployeeController.php'
cat > app/Http/Controllers/EmployeeController.php << 'MARK1'
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::query()
            ->with('department')
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->position, fn ($q) => $q->where('position', 'like', "%{$request->position}%"))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%")
                        ->orWhere('employee_id', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(20);

        return view('employees.index', [
            'employees' => $employees,
            'departments' => Department::all(),
        ]);
    }

    public function create()
    {
        return view('employees.create', [
            'departments' => Department::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:50|unique:employees,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $validated['is_active'] = true;

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'departments' => Department::all(),
            'assignedAssets' => $employee->assets()->with('category')->get(),
            'availableAssets' => \App\Models\Asset::where(function ($q) use ($employee) {
                $q->whereNull('assigned_employee_id')->orWhere('assigned_employee_id', '!=', $employee->id);
            })->orderBy('asset_tag')->get(),
        ]);
    }

    public function assignAsset(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'remarks' => 'nullable|string',
        ]);

        $asset = \App\Models\Asset::findOrFail($validated['asset_id']);

        // Close out this asset's previous assignment, if any
        $asset->assignments()->whereNull('returned_date')->update(['returned_date' => now()]);

        \App\Models\AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
            'assigned_by' => auth()->id(),
            'assigned_date' => now(),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $asset->update([
            'assigned_employee_id' => $employee->id,
            'department_id' => $employee->department_id ?? $asset->department_id,
        ]);

        return redirect()->route('employees.edit', $employee)->with('success', 'Asset assigned successfully.');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'required|boolean',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['is_active' => false]);

        return redirect()->route('employees.index')->with('success', 'Employee marked inactive.');
    }
}

MARK1

echo 'Writing resources/views/employees/edit.blade.php'
cat > resources/views/employees/edit.blade.php << 'MARK2'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Employee — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128421;</span> Assets
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">EDIT EMPLOYEE</h1>
                    <p class="text-xs text-gray-400">{{ $employee->full_name }}</p>
                </div>
                <a href="{{ route('employees.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-2xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employees.update', $employee) }}" class="max-w-2xl">
                @csrf
                @method('PUT')
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FIRST NAME *</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">LAST NAME *</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMAIL</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">PHONE</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— None —</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id) == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position) }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="is_active" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="1" @selected(old('is_active', $employee->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $employee->is_active) == 0)>Inactive</option>
                        </select>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            SAVE CHANGES
                        </button>
                    </div>
                </div>
            </form>

            <div class="max-w-2xl mt-6">
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assigned Assets</h2>

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
                    @endif

                    <table class="min-w-full text-xs divide-y divide-rose-100 mb-4">
                        <thead>
                            <tr class="text-left text-gray-500 uppercase">
                                <th class="py-2">Asset Tag</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50">
                            @forelse ($assignedAssets as $asset)
                                <tr>
                                    <td class="py-2 font-mono">
                                        <a href="{{ route('assets.show', $asset) }}" class="text-pink-600 hover:underline">{{ $asset->asset_tag }}</a>
                                    </td>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->category->name ?? '—' }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $asset->status)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-400">No assets assigned yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <form method="POST" action="{{ route('employees.assign-asset', $employee) }}" class="flex gap-2 items-end border-t border-rose-100 pt-4">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGN ANOTHER ASSET</label>
                            <select name="asset_id" required class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select an asset</option>
                                @foreach ($availableAssets as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->asset_tag }} — {{ $asset->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            ASSIGN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

MARK2

echo 'Assign-more-assets feature added!'