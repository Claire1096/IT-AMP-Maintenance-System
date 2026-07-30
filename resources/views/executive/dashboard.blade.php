@extends('layouts.shell')
@section('title', 'Executive Dashboard')
@section('content')

    <div class="text-center mb-8">
        <div class="w-14 h-14 mx-auto rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-lg mb-3">EM</div>
        <h1 class="text-xl font-bold text-gray-800">Welcome to E<span class="text-pink-600">M</span> Power Beautiful Skin Corporation</h1>
        <p class="text-xs text-gray-400 mt-1">Executives' Personal Asset Management Tracking System</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white border border-rose-100 rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-700">IT ASSETS INVENTORY</h2>
                <span class="text-xs text-gray-400">Total: {{ $totalItAssets }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-3 py-2">Asset No.</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Department</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($itAssets as $asset)
                            <tr class="hover:bg-rose-50">
                                <td class="px-3 py-2 font-mono">{{ $asset->asset_tag }}</td>
                                <td class="px-3 py-2">{{ $asset->category->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $asset->department->name ?? '—' }}</td>
                                <td class="px-3 py-2">
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
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">No IT assets yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-3">
                <a href="{{ route('assets.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">VIEW ALL</a>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-700">FACILITY ASSET INVENTORY</h2>
                <span class="text-xs text-gray-400">Total: {{ $totalFacilityAssets }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-3 py-2">Item Tag</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Department</th>
                            <th class="px-3 py-2">Condition</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($facilityItems as $item)
                            <tr class="hover:bg-rose-50">
                                <td class="px-3 py-2 font-mono">{{ $item->item_tag }}</td>
                                <td class="px-3 py-2">{{ $item->name }}</td>
                                <td class="px-3 py-2">{{ $item->department->name ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span @class([
                                        'px-2 py-1 rounded-full font-semibold text-[10px]',
                                        'bg-green-500 text-white' => $item->condition === 'good',
                                        'bg-yellow-500 text-white' => $item->condition === 'fair',
                                        'bg-red-500 text-white' => $item->condition === 'poor',
                                    ])>
                                        {{ strtoupper($item->condition) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">No facility items yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-3">
                <a href="{{ route('facility-items.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">VIEW ALL</a>
            </div>
        </div>

    </div>

@endsection
