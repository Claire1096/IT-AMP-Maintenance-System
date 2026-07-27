@extends('layouts.shell')

@section('title', 'Facility Inventory')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">FACILITY INVENTORY</h1>
            <p class="text-xs text-gray-400">General facility physical inventory tracking</p>
        </div>
        <a href="{{ route('facility-items.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD ITEM</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center text-lg">&#128230;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Total</div>
                <div class="text-xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-lg">&#9989;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">In Use</div>
                <div class="text-xl font-bold">{{ $stats['in_use'] }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-lg">&#128230;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">In Storage</div>
                <div class="text-xl font-bold">{{ $stats['in_storage'] }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg">&#9888;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Damaged</div>
                <div class="text-xl font-bold">{{ $stats['damaged'] }}</div>
            </div>
        </div>
    </div>

    <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
        <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">&#128269; Filter Items</h2>
        <div class="grid grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                <select name="category" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET TYPE</label>
                <select name="asset_type" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Asset Types</option>
                @foreach ($assetTypes as $type)
                    <option value="{{ $type }}" @selected(request('asset_type') === $type)>{{ ucfirst($type) }}</option>
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
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag or name..." class="w-full text-xs border-gray-300 rounded-md">
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('facility-items.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR FILTERS</a>
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">+ APPLY FILTERS</button>
        </div>
    </form>

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Item Tag</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Asset Type</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Condition</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($items as $item)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-mono">{{ $item->item_tag }}</td>
                        <td class="px-4 py-3">{{ $item->name }}</td>
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $item->category)) }}</td>
                        <td class="px-4 py-3">{{ $item->asset_type ? ucfirst($item->asset_type) : '—' }}</td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ $item->department->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full font-semibold text-[10px]',
                                'bg-green-500 text-white' => $item->condition === 'good',
                                'bg-yellow-500 text-white' => $item->condition === 'fair',
                                'bg-red-500 text-white' => $item->condition === 'poor',
                            ])>
                                {{ strtoupper($item->condition) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            {{ $item->location ? ($item->location->building ? $item->location->building->name . ' — ' . $item->location->name : $item->location->name) : '—' }}
                         </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('facility-items.edit', $item) }}" title="Edit">&#9998;</a>
                                <a href="{{ route('facility-items.show', $item) }}" title="View">&#128065;</a>
                                <form method="POST" action="{{ route('facility-items.destroy', $item) }}" onsubmit="return confirm('Remove this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete">&#128465;</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No facility items registered yet. <a href="{{ route('facility-items.create') }}" class="text-pink-600 hover:underline">Register the first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->withQueryString()->links() }}
    </div>
@endsection