@extends('layouts.shell')

@section('title', 'Facility Inventory Report')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">FACILITY INVENTORY REPORT</h1>
            <p class="text-xs text-gray-400">Full list of registered facility items</p>
        </div>
    </div>

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Item Tag</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Condition</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($items as $item)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-mono">{{ $item->item_tag }}</td>
                        <td class="px-4 py-3">{{ $item->name }}</td>
                        <td class="px-4 py-3">{{ $item->category }}</td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ $item->department->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ ucwords($item->condition) }}</td>
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $item->status)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No items to report.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection