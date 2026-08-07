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

<div class="flex justify-end mb-3">
    {{ $assets->withQueryString()->links() }}
</div>

<div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
    <table class="min-w-full divide-y divide-rose-100 text-xs">
        <thead class="bg-pink-100">
            <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-3">Asset No.</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Assigned To</th>
                <th class="px-4 py-3">Department</th>
                <th class="px-4 py-3">Location</th>
                <th class="px-4 py-3">Building</th>
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
                    <td class="px-4 py-3">{{ $asset->category ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $asset->assignedEmployee->full_name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $asset->department->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $asset->location->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $asset->location->building->name ?? '—' }}</td>
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
                    <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                        No assets registered yet. <a href="{{ route('assets.create') }}" class="text-pink-600 hover:underline">Register the first one.</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
