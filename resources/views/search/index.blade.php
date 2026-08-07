<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-8 h-8 rounded-full object-cover">
            <div class="leading-tight">
                <div class="font-semibold text-sm">E<span class="text-pink-600">M</span> Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('search') }}"><input type="text" name="q" value="{{ $query }}" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56"></form>
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">SEARCH RESULTS</h1>
                <p class="text-xs text-gray-400">Showing results for "{{ $query }}"</p>
            </div>

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Assets ({{ $assets->count() }})</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase">
                            <th class="py-2">Asset Tag</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Assigned To</th>
                            <th>Department</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($assets as $asset)
                            <tr>
                                <td class="py-2 font-mono">{{ $asset->asset_tag }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->category->name ?? '—' }}</td>
                                <td>{{ $asset->assignedEmployee->full_name ?? '—' }}</td>
                                <td>{{ $asset->department->name ?? '—' }}</td>
                                <td><a href="{{ route('assets.show', $asset) }}" class="text-pink-600 hover:underline">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No matching assets.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Employees ({{ $employees->count() }})</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase">
                            <th class="py-2">Employee ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="py-2 font-mono">{{ $employee->employee_id ?? '—' }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->department->name ?? '—' }}</td>
                                <td>{{ $employee->position ?? '—' }}</td>
                                <td><a href="{{ route('employees.edit', $employee) }}" class="text-pink-600 hover:underline">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No matching employees.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

