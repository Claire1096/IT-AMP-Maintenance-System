#!/bin/bash
set -e
mkdir -p resources/views/employees
echo 'Writing employees/index.blade.php'
cat > resources/views/employees/index.blade.php << 'IDX_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees — EM Power Beautiful Skin</title>
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
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <span class="text-gray-400">&#128276;</span>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-300"></div>
                <div class="leading-tight text-xs">
                    <div class="font-semibold">{{ auth()->user()->name ?? 'Admin user' }}</div>
                    <div class="text-gray-400">{{ auth()->user()->role ?? 'administrator' }}</div>
                </div>
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
                            <th class="px-4 py-3">Employee ID</th>
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
                                <td class="px-4 py-3 font-mono">{{ $employee->employee_id ?? '—' }}</td>
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
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
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
        </div>
    </div>
</body>
</html>
IDX_EOF
echo 'Writing employees/create.blade.php'
cat > resources/views/employees/create.blade.php << 'CRT_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Employee — EM Power Beautiful Skin</title>
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
                    <h1 class="text-lg font-bold">ADD EMPLOYEE</h1>
                    <p class="text-xs text-gray-400">Employees / Add new employee</p>
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

            <form method="POST" action="{{ route('employees.store') }}" class="max-w-2xl">
                @csrf
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMPLOYEE ID</label>
                            <input type="text" name="employee_id" value="{{ old('employee_id') }}" placeholder="e.g. EMP030" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FIRST NAME *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">LAST NAME *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMAIL</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">PHONE</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0935-456-1236" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
                        <input type="text" name="position" value="{{ old('position') }}" placeholder="e.g. IT Support" class="w-full text-xs border-gray-300 rounded-md">
                    </div>

                    <div class="text-right">
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            SAVE EMPLOYEE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
CRT_EOF
echo 'Writing employees/edit.blade.php'
cat > resources/views/employees/edit.blade.php << 'EDT_EOF'
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
        </div>
    </div>
</body>
</html>
EDT_EOF
echo 'All employee views created!'
