<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
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
            'positions' => Position::all(),
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

        $employee = Employee::create($validated);

        if ($request->filled('redirect_to') && str_starts_with($request->redirect_to, '/')) {
            return redirect($request->redirect_to)->with('success', "Employee \"{$employee->full_name}\" added.");
        }

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = true;

        $employee = Employee::create($validated);

        return response()->json([
            'id' => $employee->id,
            'name' => $employee->full_name,
        ]);
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

