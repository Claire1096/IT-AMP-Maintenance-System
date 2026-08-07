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

<div class="mt-6 mb-4 flex justify-end items-center gap-3 pr-2">
    @if ($items->onFirstPage())
        <span class="w-14 h-11 flex items-center justify-center rounded-full bg-[#13000A] text-white text-lg opacity-40 cursor-not-allowed">&#8249;</span>
    @else
        <a href="{{ $items->previousPageUrl() }}" data-page-link class="w-14 h-11 flex items-center justify-center rounded-full bg-[#13000A] text-white text-lg hover:opacity-80">&#8249;</a>
    @endif

    <span class="w-14 h-11 flex items-center justify-center rounded-full border-2 border-gray-300 text-sm font-bold">{{ $items->currentPage() }}</span>

    @if ($items->hasMorePages())
        <a href="{{ $items->nextPageUrl() }}" data-page-link class="w-14 h-11 flex items-center justify-center rounded-full bg-[#13000A] text-white text-lg hover:opacity-80">&#8250;</a>
    @else
        <span class="w-14 h-11 flex items-center justify-center rounded-full bg-[#13000A] text-white text-lg opacity-40 cursor-not-allowed">&#8250;</span>
    @endif
</div>

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
                @if (auth()->user()->role === 'finance_supervisor')
                    <th class="px-4 py-3">Discrepancies</th>
                @endif
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
                    @if (auth()->user()->role === 'finance_supervisor')
                        <td class="px-4 py-3">
                            @if ($item->status === 'missing')
                                <span class="px-2 py-1 rounded-full font-semibold text-[10px] bg-red-500 text-white">
                                    MISSING SINCE {{ $item->missing_since?->format('M d, Y') }}
                                </span>
                            @elseif ($item->maintenances->isNotEmpty())
                                <span class="px-2 py-1 rounded-full font-semibold text-[10px] bg-yellow-500 text-white">
                                    OVERDUE CHECK ({{ now()->diffInDays($item->maintenances->first()->due_date) }}d)
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    @endif
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
                    <td colspan="{{ auth()->user()->role === 'finance_supervisor' ? 10 : 9 }}" class="px-4 py-8 text-center text-gray-400">
                        No facility items registered yet. <a href="{{ route('facility-items.create') }}" class="text-pink-600 hover:underline">Register the first one.</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>