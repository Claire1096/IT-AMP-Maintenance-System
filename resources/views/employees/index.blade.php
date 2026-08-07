@extends('layouts.shell')
@section('title', 'Employees')
@section('content')

    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EMPLOYEES</h1>
            <p class="text-xs text-gray-400">All Employees</p>
        </div>
        <a href="{{ route('employees.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD EMPLOYEE</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
        <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">&#128269; Filter Employees</h2>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
                <input type="text" name="position" value="{{ request('position') }}" placeholder="e.g. IT Support" class="w-full text-xs border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR FILTERS</a>
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">+ APPLY FILTERS</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Position</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($employees as $employee)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3">{{ $employee->full_name }}</td>
                        <td class="px-4 py-3">{{ $employee->email ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $employee->department->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $employee->position ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $employee->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full font-semibold text-[10px]',
                                'bg-green-500 text-white' => $employee->is_active,
                                'bg-gray-400 text-white' => !$employee->is_active,
                            ])>
                                {{ $employee->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('employees.edit', $employee) }}" title="Edit">&#9998;</a>
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Mark this employee inactive?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Deactivate">&#128465;</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            No employees added yet. <a href="{{ route('employees.create') }}" class="text-pink-600 hover:underline">Add the first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $employees->withQueryString()->links() }}
    </div>

@endsection