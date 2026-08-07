<div class="grid grid-cols-3 gap-4 mb-6">
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
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Active</div>
            <div class="text-xl font-bold">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg">&#10060;</div>
        <div>
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Missing</div>
            <div class="text-xl font-bold">{{ $stats['missing'] }}</div>
        </div>
    </div>
</div>

<div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
    <table class="min-w-full divide-y divide-rose-100 text-xs">
        <thead class="bg-pink-100">
            <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-3">Item Tag</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Asset Type</th>
                <th class="px-4 py-3">Qty</th>
                <th class="px-4 py-3">Department</th>
                <th class="px-4 py-3">Discrepancies</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-50">
            @forelse ($items as $item)
                <tr class="hover:bg-rose-50">
                    <td class="px-4 py-3 font-mono">{{ $item->item_tag }}</td>
                    <td class="px-4 py-3">{{ $item->name }}</td>
                    <td class="px-4 py-3">{{ $item->asset_type ? ucfirst($item->asset_type) : '—' }}</td>
                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                    <td class="px-4 py-3">{{ $item->department->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($item->missing_quantity > 0)
                            <span class="px-2 py-1 rounded-full font-semibold text-[10px] bg-red-500 text-white">
                                {{ $item->missing_quantity }} MISSING @if($item->missing_since) SINCE {{ $item->missing_since->format('M d, Y') }} @endif
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('finance-items.show', $item) }}" class="px-3 py-1 border border-gray-300 text-gray-600 text-[10px] font-semibold rounded-full hover:bg-gray-50">VIEW</a>
                            <a href="{{ route('finance-items.edit', $item) }}" class="px-3 py-1 border border-blue-200 text-blue-600 text-[10px] font-semibold rounded-full hover:bg-blue-50">EDIT</a>
                            <form method="POST" action="{{ route('finance-items.destroy', $item) }}" class="inline" onsubmit="return confirm('Remove this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 border border-red-200 text-red-600 text-[10px] font-semibold rounded-full hover:bg-red-50">DELETE</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">No items yet. Import your list or add one manually.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 flex items-center justify-between">
    <span class="text-xs text-gray-500">Page {{ $items->currentPage() }}</span>
    <div class="flex gap-2">
        @if ($items->onFirstPage())
            <span class="px-4 py-1.5 border border-gray-200 text-gray-300 text-xs font-semibold rounded-full cursor-not-allowed">&#8249; Previous</span>
        @else
            <a href="{{ $items->previousPageUrl() }}" data-page-link class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full hover:bg-gray-50">&#8249; Previous</a>
        @endif

        @if ($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" data-page-link class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">Next &#8250;</a>
        @else
            <span class="px-4 py-1.5 bg-gray-200 text-gray-400 text-xs font-semibold rounded-full cursor-not-allowed">Next &#8250;</span>
        @endif
    </div>
</div>
