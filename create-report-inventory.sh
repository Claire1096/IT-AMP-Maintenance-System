#!/bin/bash
set -e
mkdir -p resources/views/reports
cat > resources/views/reports/inventory.blade.php << 'BLADE_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Inventory Report — EM Power Beautiful Skin</title>
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
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">ASSET INVENTORY REPORT</h1>
                <p class="text-xs text-gray-400">Reports / Asset Inventory</p>
            </div>

            {{-- Report nav tabs --}}
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('reports.inventory') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-pink-600 text-white">Inventory</a>
                <a href="{{ route('reports.preventive-maintenance') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-rose-200 text-gray-600">Preventive Maintenance</a>
                <a href="{{ route('reports.warranty-expiration') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-rose-200 text-gray-600">Warranty Expiration</a>
                <a href="{{ route('reports.repair-history') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-rose-200 text-gray-600">Repair History</a>
                <a href="{{ route('reports.asset-assignment') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-rose-200 text-gray-600">Asset Assignment</a>
                <a href="{{ route('reports.annual-summary') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-rose-200 text-gray-600">Annual Summary</a>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                        <select name="category_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All</option>
                            @foreach (\App\Models\AssetCategory::all() as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All</option>
                            @foreach (\App\Models\Department::all() as $department)
                                <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All</option>
                            @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('reports.inventory') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR</a>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">FILTER</button>
                </div>
            </form>

            <div class="mb-3 text-xs text-gray-500">{{ $assets->count() }} asset(s) found</div>

            <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
                <table class="min-w-full divide-y divide-rose-100 text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset Tag</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-rose-50">
                                <td class="px-4 py-3 font-mono">{{ $asset->asset_tag }}</td>
                                <td class="px-4 py-3">{{ $asset->name }}</td>
                                <td class="px-4 py-3">{{ $asset->category->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->assignedEmployee->full_name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->department->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->location->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No assets match this filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
BLADE_EOF
echo 'reports/inventory.blade.php created!'
