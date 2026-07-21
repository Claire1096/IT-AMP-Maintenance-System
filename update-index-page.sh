#!/bin/bash
set -e
mkdir -p resources/views/assets
cat > resources/views/assets/index.blade.php << 'BLADE_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assets — EM Power Beautiful Skin</title>
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
                    <h1 class="text-lg font-bold">IT ASSETS</h1>
                    <p class="text-xs text-gray-400">Asset Management / All assets</p>
                </div>
                <a href="{{ route('assets.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ REGISTER ASSET</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
            @endif

            <form method="GET" class="mb-4 bg-white border border-rose-100 p-4 rounded-xl shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag, name, serial..."
                           class="text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                    <select name="status" class="text-xs border-gray-300 rounded-md">
                        <option value="">All</option>
                        @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-full">Filter</button>
                <a href="{{ route('assets.index') }}" class="px-4 py-2 text-xs text-gray-500">Clear</a>
            </form>

            <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
                <table class="min-w-full divide-y divide-rose-100 text-xs">
                    <thead class="bg-rose-50">
                        <tr class="text-left font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset Tag</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Warranty</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-rose-50">
                                <td class="px-4 py-3 font-mono">{{ $asset->asset_tag }}</td>
                                <td class="px-4 py-3">{{ $asset->name }}</td>
                                <td class="px-4 py-3">{{ $asset->category->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->assignedEmployee->full_name ?? '— unassigned —' }}</td>
                                <td class="px-4 py-3">{{ $asset->department->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full font-medium',
                                        'bg-green-100 text-green-800' => $asset->status === 'active',
                                        'bg-yellow-100 text-yellow-800' => $asset->status === 'under_repair',
                                        'bg-gray-200 text-gray-700' => $asset->status === 'for_disposal',
                                        'bg-red-100 text-red-800' => $asset->status === 'lost',
                                    ])>
                                        {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($asset->warranty_expiration)
                                        @if ($asset->isUnderWarranty())
                                            <span class="text-green-700">until {{ $asset->warranty_expiration->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600">expired {{ $asset->warranty_expiration->format('M d, Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-pink-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
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
BLADE_EOF
echo 'index.blade.php updated with branded design!'
