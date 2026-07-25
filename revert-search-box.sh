#!/bin/bash
set -e
mkdir -p resources/views/employees resources/views/assets

echo 'Writing resources/views/dashboard.blade.php'
cat > resources/views/dashboard.blade.php << 'MARKA'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
            <div class="mb-6">
                <h1 class="text-lg font-bold">DASHBOARD</h1>
                <p class="text-xs text-gray-400">Overview of IT assets and maintenance</p>
            </div>

            {{-- KPI cards --}}
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center text-lg">&#128187;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Assets</div>
                        <div class="text-xl font-bold">{{ $totalAssets }}</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-lg">&#9989;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Active Assets</div>
                        <div class="text-xl font-bold">{{ $activeAssets }}</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-lg">&#128295;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Under Repair</div>
                        <div class="text-xl font-bold">{{ $underRepair }}</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-lg">&#128197;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Maintenance Due (Month)</div>
                        <div class="text-xl font-bold">{{ $maintenanceDueThisMonth }}</div>
                    </div>
                </div>
            </div>

            {{-- Warranty row --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-lg">&#9203;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Warranty Expiring Soon (30 days)</div>
                        <div class="text-xl font-bold">{{ $warrantyExpiringSoon }}</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg">&#10060;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Warranty Expired</div>
                        <div class="text-xl font-bold">{{ $warrantyExpired }}</div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assets by Department</h2>
                    <canvas id="deptChart" height="220"></canvas>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Monthly Maintenance Completed</h2>
                    <canvas id="maintenanceChart" height="220"></canvas>
                </div>
            </div>

            {{-- Assets by category --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assets by Category</h2>
                <canvas id="categoryChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <script>
        const deptLabels = @json($assetsByDepartment->pluck('name'));
        const deptData = @json($assetsByDepartment->pluck('assets_count'));
        new Chart(document.getElementById('deptChart'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{ label: 'Assets', data: deptData, backgroundColor: '#ec4899' }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const maintLabels = @json($monthlyMaintenance->pluck('month'));
        const maintData = @json($monthlyMaintenance->pluck('completed_count'));
        new Chart(document.getElementById('maintenanceChart'), {
            type: 'line',
            data: {
                labels: maintLabels,
                datasets: [{ label: 'Completed', data: maintData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.3 }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const catLabels = @json($assetsByCategory->pluck('category.name'));
        const catData = @json($assetsByCategory->pluck('total'));
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{ label: 'Assets', data: catData, backgroundColor: '#f97316' }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>

MARKA

echo 'Writing resources/views/employees/index.blade.php'
cat > resources/views/employees/index.blade.php << 'MARKB'
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
            <x-notification-bell />
            <x-account-menu />
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

MARKB

echo 'Writing resources/views/assets/create.blade.php'
cat > resources/views/assets/create.blade.php << 'MARKC'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Asset — EM Power Beautiful Skin</title>
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
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
                    <h1 class="text-lg font-bold">ADD ASSET</h1>
                    <p class="text-xs text-gray-400">Asset Management / Add new asset</p>
                </div>
                <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-3xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('assets.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-6 max-w-3xl">

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET ID</label>
                                <input type="text" disabled placeholder="auto-generated after saving"
                                       class="w-full text-xs border-gray-200 rounded-md bg-gray-50 text-gray-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY *</label>
                                <select name="category_id" required class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="eg. Dell Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="eg. Dell..."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                                <input type="text" name="model" value="{{ old('model') }}" placeholder="eg. Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="eg. 42H5-Y642-W524785.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">QR CODE</label>
                            <div class="text-xs text-gray-400 border border-dashed border-gray-300 rounded-md px-3 py-2">
                                Generated automatically after saving
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">NOTES</label>
                            <textarea name="notes" rows="2" class="w-full text-xs border-gray-300 rounded-md">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Asset Assignment</h2>

                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
                                <select name="assigned_employee_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— Unassigned —</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                                <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                            {{ $location->building->name ?? '' }} — {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#128722; Purchase Details</h3>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                                    <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                                        <option value="">Select</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                                    <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                SAVE CHANGES
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

MARKC

echo 'Writing resources/views/assets/index.blade.php'
cat > resources/views/assets/index.blade.php << 'MARKD'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Management — EM Power Beautiful Skin</title>
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
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
                    <h1 class="text-lg font-bold">ASSET MANAGEMENT</h1>
                    <p class="text-xs text-gray-400">All registered assets</p>
                </div>
                <a href="{{ route('assets.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD ASSET</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
            @endif

            {{-- KPI cards --}}
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center text-lg">&#128187;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Assets</div>
                        <div class="text-xl font-bold">{{ $stats['total'] }}</div>
                        <div class="text-[10px] text-gray-400">All registered assets</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-lg">&#9989;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Active Assets</div>
                        <div class="text-xl font-bold">{{ $stats['active'] }}</div>
                        <div class="text-[10px] text-gray-400">All active assets</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-lg">&#128295;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Under Repair</div>
                        <div class="text-xl font-bold">{{ $stats['under_repair'] }}</div>
                        <div class="text-[10px] text-gray-400">All under repair assets</div>
                    </div>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg">&#128737;</div>
                    <div>
                        <div class="text-[10px] font-semibold text-gray-500 uppercase">Expiring Soon</div>
                        <div class="text-xl font-bold">{{ $stats['expiring_soon'] }}</div>
                        <div class="text-[10px] text-gray-400">Warranty within 30 days</div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
                <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">&#128269; Filter Assets</h2>
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                        <select name="category_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                        <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>
                                    {{ $location->building->name ?? '' }} — {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR FILTERS</a>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">+ APPLY FILTERS</button>
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
                <table class="min-w-full divide-y divide-rose-100 text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset No.</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Purchase Date</th>
                            <th class="px-4 py-3">Expiration</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-rose-50">
                                <td class="px-4 py-3 font-mono">{{ $asset->asset_tag }}</td>
                                <td class="px-4 py-3">{{ $asset->category->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->assignedEmployee->full_name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->department->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->location->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($asset->purchase_date)->format('m - d - y') }}</td>
                                <td class="px-4 py-3">{{ optional($asset->warranty_expiration)->format('m - d - y') }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full font-semibold text-[10px]',
                                        'bg-green-500 text-white' => $asset->status === 'active',
                                        'bg-yellow-500 text-white' => $asset->status === 'under_repair',
                                        'bg-gray-400 text-white' => $asset->status === 'for_disposal',
                                        'bg-red-500 text-white' => $asset->status === 'lost',
                                    ])>
                                        {{ strtoupper(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('assets.edit', $asset) }}" title="Edit">&#9998;</a>
                                        <a href="{{ route('assets.show', $asset) }}" title="View">&#128065;</a>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Remove this asset?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete">&#128465;</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                                    No assets registered yet. <a href="{{ route('assets.create') }}" class="text-pink-600 hover:underline">Register the first one.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $assets->withQueryString()->links() }}
            </div>
        </div>
    </div>
</body>
</html>

MARKD

echo 'Writing resources/views/assets/show.blade.php'
cat > resources/views/assets/show.blade.php << 'MARKE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->asset_tag }} — EM Power Beautiful Skin</title>
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
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
                    <h1 class="text-lg font-bold">{{ $asset->asset_tag }} — {{ $asset->name }}</h1>
                    <p class="text-xs text-gray-400">Asset Management / Asset details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-1.5 bg-gray-200 text-gray-800 text-xs font-semibold rounded-full">EDIT</a>
                    <a href="{{ route('maintenance.create', $asset) }}" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-full">SCHEDULE MAINTENANCE</a>
                    <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-4xl">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-2 gap-6 max-w-4xl mb-6">

                {{-- Asset Details card --}}
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Details</h2>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->category->name ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->brand }} {{ $asset->model }}</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->serial_number ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
                        <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->assignedEmployee->full_name ?? 'unassigned' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->department->name ?? 'unassigned' }}</div>
                    </div>

                    <form method="POST" action="{{ route('assets.reassign', $asset) }}">
                        @csrf
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">REASSIGN TO</label>
                        <select name="employee_id" required class="w-full text-xs border-gray-300 rounded-md mb-3">
                            <option value="">unassigned</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                        <div class="text-right">
                            <button type="submit" class="px-5 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                REASSIGN
                            </button>
                        </div>
                    </form>
                </div>

                {{-- QR Code card --}}
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col items-center justify-center text-center">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Asset QR Code</h2>
                    @if ($asset->qr_code_path)
                        <img src="{{ Storage::url($asset->qr_code_path) }}" alt="QR code" class="w-48 h-48 mb-3">
                    @else
                        <div class="w-48 h-48 mb-3 flex items-center justify-center text-gray-300 border border-dashed border-gray-300 rounded-md">
                            No QR generated
                        </div>
                    @endif
                    <p class="text-xs font-semibold text-gray-700">ASSET ID: {{ $asset->asset_tag }}</p>
                </div>
            </div>

            {{-- Procurement info --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Procurement</h2>
                <div class="grid grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                        {{ $asset->supplier->name ?? '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                        {{ optional($asset->purchase_date)->format('M d, Y') ?? '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                        {{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY</label>
                        <span class="{{ $asset->isUnderWarranty() ? 'text-green-700' : 'text-red-600' }}">
                            {{ optional($asset->warranty_expiration)->format('M d, Y') ?? '—' }}
                        </span>
                    </div>
                </div>
                @if ($asset->notes)
                    <div class="mt-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">NOTES</label>
                        <p class="text-xs">{{ $asset->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Maintenance schedules --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Preventive Maintenance</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-[10px] text-gray-500 uppercase">
                            <th class="py-2">Type</th>
                            <th>Frequency</th>
                            <th>Next Date</th>
                            <th>Status</th>
                            <th>Technician</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($asset->maintenanceSchedules as $schedule)
                            <tr>
                                <td class="py-2">{{ $schedule->maintenance_type }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                <td>{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                                <td>{{ ucwords($schedule->status) }}</td>
                                <td>{{ $schedule->technician->name ?? '—' }}</td>
                                <td>
                                    @if (in_array($schedule->status, ['scheduled', 'overdue']))
                                        <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                                            @csrf
                                            <button class="text-pink-600 hover:underline">Mark Complete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No maintenance scheduled yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Repair history --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Repair History</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-[10px] text-gray-500 uppercase">
                            <th class="py-2">Reported</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Cost</th>
                            <th>Downtime (hrs)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($asset->repairHistories as $repair)
                            <tr>
                                <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                                <td>{{ Str::limit($repair->issue_description, 60) }}</td>
                                <td>{{ ucwords($repair->status) }}</td>
                                <td>{{ number_format($repair->cost, 2) }}</td>
                                <td>{{ $repair->downtime_hours ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No repairs logged yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Assignment & movement history --}}
            <div class="grid grid-cols-2 gap-6 max-w-4xl">
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Assignment History</h2>
                    <ul class="text-xs space-y-2">
                        @forelse ($asset->assignments as $assignment)
                            <li class="border-b border-rose-50 pb-2">
                                {{ $assignment->employee->full_name ?? '—' }}
                                <span class="text-gray-400 text-[10px] block">
                                    {{ $assignment->assigned_date->format('M d, Y') }}
                                    @if ($assignment->returned_date) – {{ $assignment->returned_date->format('M d, Y') }} @endif
                                </span>
                            </li>
                        @empty
                            <li class="text-gray-400">No assignment history.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Movement History</h2>
                    <ul class="text-xs space-y-2">
                        @forelse ($asset->movements as $movement)
                            <li class="border-b border-rose-50 pb-2">
                                {{ $movement->fromLocation->name ?? 'Unknown' }} → {{ $movement->toLocation->name ?? 'Unknown' }}
                                <span class="text-gray-400 text-[10px] block">{{ $movement->moved_at->format('M d, Y g:i A') }}</span>
                            </li>
                        @empty
                            <li class="text-gray-400">No movement history.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

MARKE

echo 'Search box reverted, Profile/Logout dropdown kept!'