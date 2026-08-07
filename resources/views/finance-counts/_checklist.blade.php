<div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
    <table class="min-w-full divide-y divide-rose-100 text-xs">
        <thead class="bg-pink-100">
            <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-3">Item Tag</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Expected Qty</th>
                <th class="px-4 py-3">Counted Qty</th>
                <th class="px-4 py-3">Department</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-50">
            @forelse ($items as $countItem)
                <tr class="hover:bg-rose-50" data-count-item-id="{{ $countItem->id }}">
                    <td class="px-4 py-3 font-mono">{{ $countItem->financeItem->item_tag ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $countItem->financeItem->name ?? '(item removed)' }}</td>
                    <td class="px-4 py-3">{{ $countItem->expected_quantity }}</td>
                    <td class="px-4 py-3">
                        <input type="number" min="0" data-counted-qty
                               value="{{ $countItem->counted_quantity }}"
                               @if ($financeCount->status !== 'open') disabled @endif
                               class="w-20 text-xs border-gray-300 rounded-md disabled:bg-gray-50 disabled:text-gray-400">
                    </td>
                    <td class="px-4 py-3">
                        <select data-dept-select @if ($financeCount->status !== 'open') disabled @endif class="text-xs border-gray-300 rounded-md disabled:bg-gray-50 disabled:text-gray-400">
                            <option value="">— None —</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected($countItem->department_id == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <span data-row-status @class([
                            'px-2 py-1 rounded-full font-semibold text-[10px]',
                            'bg-green-500 text-white' => $countItem->checked_at,
                            'bg-gray-200 text-gray-500' => !$countItem->checked_at,
                        ])>
                            {{ $countItem->checked_at ? 'CHECKED' : 'UNCHECKED' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">No items match this filter.</td>
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
