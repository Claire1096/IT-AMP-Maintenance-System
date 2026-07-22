#!/bin/bash
set -e
mkdir -p resources/views/reports
echo Writing reports/preventive-maintenance.blade.php
cat > resources/views/reports/preventive-maintenance.blade.php << REPORTEOF1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preventive Maintenance Report — EM Power Beautiful Skin</title>
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Preventive Maintenance Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Preventive Maintenance Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Date Range</span> : {{ request('from') ?? 'All time' }} — {{ request('to') ?? 'Present' }}</div>
                        <div><span class="font-semibold">Status</span> : {{ request('status') ? ucwords(str_replace('_', ' ', request('status'))) : 'All Statuses' }}</div>
                        <div><span class="font-semibold">Total Scheduled</span> : {{ $schedules->count() }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Frequency</th>
                                <th class="py-2">Scheduled</th>
                                <th class="py-2">Next Date</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Technician</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schedules as $schedule)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $schedule->asset->asset_tag }}</td>
                                    <td class="py-2">{{ $schedule->maintenance_type }}</td>
                                    <td class="py-2">{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                    <td class="py-2">{{ $schedule->scheduled_date->format('M d, Y') }}</td>
                                    <td class="py-2">{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                                    <td class="py-2">{{ ucwords(str_replace('_', ' ', $schedule->status)) }}</td>
                                    <td class="py-2">{{ $schedule->technician->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-gray-400">No maintenance records match this filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}" selected>Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FROM DATE</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">TO DATE</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                            <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">All</option>
                                @foreach (['scheduled', 'in_progress', 'completed', 'overdue', 'skipped'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
REPORTEOF1
echo Writing reports/warranty-expiration.blade.php
cat > resources/views/reports/warranty-expiration.blade.php << REPORTEOF2
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warranty Expiration Report — EM Power Beautiful Skin</title>
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Warranty Expiration Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Warranty Expiration Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Window</span> : Expiring within {{ $withinDays }} days</div>
                        <div><span class="font-semibold">Total Assets</span> : {{ $assets->count() }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset Tag</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Category</th>
                                <th class="py-2">Department</th>
                                <th class="py-2">Warranty Expiration</th>
                                <th class="py-2">Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $asset->asset_tag }}</td>
                                    <td class="py-2">{{ $asset->name }}</td>
                                    <td class="py-2">{{ $asset->category->name ?? '—' }}</td>
                                    <td class="py-2">{{ $asset->department->name ?? '—' }}</td>
                                    <td class="py-2">{{ $asset->warranty_expiration->format('M d, Y') }}</td>
                                    <td class="py-2 {{ now()->diffInDays($asset->warranty_expiration, false) < 0 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ now()->diffInDays($asset->warranty_expiration, false) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No assets expiring in this window.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}" selected>Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">EXPIRING WITHIN (DAYS)</label>
                            <input type="number" name="within_days" value="{{ $withinDays }}" min="1" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
REPORTEOF2
echo Writing reports/repair-history.blade.php
cat > resources/views/reports/repair-history.blade.php << REPORTEOF3
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Repair History Report — EM Power Beautiful Skin</title>
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Repair History Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Repair History Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Date Range</span> : {{ request('from') ?? 'All time' }} — {{ request('to') ?? 'Present' }}</div>
                        <div><span class="font-semibold">Total Repairs</span> : {{ $repairs->count() }}</div>
                        <div><span class="font-semibold">Total Repair Cost</span> : {{ number_format($totalCost, 2) }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset</th>
                                <th class="py-2">Reported</th>
                                <th class="py-2">Issue</th>
                                <th class="py-2">Technician</th>
                                <th class="py-2">Cost</th>
                                <th class="py-2">Downtime (hrs)</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($repairs as $repair)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $repair->asset->asset_tag }}</td>
                                    <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                                    <td class="py-2">{{ \Illuminate\Support\Str::limit($repair->issue_description, 40) }}</td>
                                    <td class="py-2">{{ $repair->technician->name ?? '—' }}</td>
                                    <td class="py-2">{{ number_format($repair->cost, 2) }}</td>
                                    <td class="py-2">{{ $repair->downtime_hours ?? '—' }}</td>
                                    <td class="py-2">{{ ucwords($repair->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-gray-400">No repairs match this filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}" selected>Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FROM DATE</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">TO DATE</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
REPORTEOF3
echo Writing reports/asset-assignment.blade.php
cat > resources/views/reports/asset-assignment.blade.php << REPORTEOF4
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Assignment Report — EM Power Beautiful Skin</title>
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Asset Assignment Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Asset Assignment Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Department</span> : {{ optional(\App\Models\Department::find(request('department_id')))->name ?? 'All Departments' }}</div>
                        <div><span class="font-semibold">Filter</span> : {{ request('active_only') ? 'Active assignments only' : 'All assignments' }}</div>
                        <div><span class="font-semibold">Total Records</span> : {{ $assignments->count() }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset</th>
                                <th class="py-2">Employee</th>
                                <th class="py-2">Department</th>
                                <th class="py-2">Assigned Date</th>
                                <th class="py-2">Returned Date</th>
                                <th class="py-2">Assigned By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $assignment->asset->asset_tag ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->employee->full_name ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->department->name ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->assigned_date->format('M d, Y') }}</td>
                                    <td class="py-2">{{ optional($assignment->returned_date)->format('M d, Y') ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->assignedBy->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No assignment records match this filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}" selected>Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">All Departments</option>
                                @foreach (\App\Models\Department::all() as $department)
                                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center gap-2 text-xs">
                                <input type="checkbox" name="active_only" value="1" @checked(request('active_only'))>
                                Active assignments only
                            </label>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
REPORTEOF4
echo Writing reports/annual-summary.blade.php
cat > resources/views/reports/annual-summary.blade.php << REPORTEOF5
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annual Asset Summary — EM Power Beautiful Skin</title>
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Annual Asset Summary</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Annual Asset Summary — {{ $year }}</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1 text-center">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">New Assets</div>
                            <div class="text-xl font-bold">{{ $newAssets }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Disposed Assets</div>
                            <div class="text-xl font-bold">{{ $disposedAssets }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Maintenance Completed</div>
                            <div class="text-xl font-bold">{{ $totalMaintenanceCompleted }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Purchase Spend</div>
                            <div class="text-xl font-bold">{{ number_format($totalSpendOnPurchases, 2) }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Repair Cost</div>
                            <div class="text-xl font-bold">{{ number_format($totalRepairCost, 2) }}</div>
                        </div>
                    </div>

                    <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">New Assets by Category</h3>
                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Category</th>
                                <th class="py-2">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byCategory as $row)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">{{ $row->category->name ?? '—' }}</td>
                                    <td class="py-2">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-6 text-center text-gray-400">No new assets registered in {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}" selected>Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">YEAR</label>
                            <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
REPORTEOF5
echo All 5 remaining reports created!
