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

