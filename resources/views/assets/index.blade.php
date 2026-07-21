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
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
