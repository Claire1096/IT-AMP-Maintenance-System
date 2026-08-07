@extends('layouts.shell')

@section('title', 'Item Details')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">{{ $item->item_tag }}</h1>
            <p class="text-xs text-gray-400">{{ $item->name }}</p>
        </div>
        <a href="{{ route('audit.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-2xl">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-2xl mb-6">
        <dl class="grid grid-cols-2 gap-4 text-xs">
            <div><dt class="text-gray-400">Asset Type</dt><dd class="font-semibold">{{ $item->asset_type ? ucfirst($item->asset_type) : '—' }}</dd></div>
            <div><dt class="text-gray-400">Department</dt><dd class="font-semibold">{{ $item->department->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Total Quantity</dt><dd class="font-semibold">{{ $item->quantity }}</dd></div>
            <div><dt class="text-gray-400">Current Quantity</dt><dd class="font-semibold">{{ $item->current_quantity }}</dd></div>
            <div><dt class="text-gray-400">Missing Quantity</dt><dd class="font-semibold text-red-600">{{ $item->missing_quantity }}</dd></div>
            <div><dt class="text-gray-400">Status</dt><dd class="font-semibold">{{ ucwords(str_replace('_', ' ', $item->status)) }}</dd></div>
        </dl>
    </div>

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl max-w-2xl">
        <div class="px-4 py-3 border-b border-rose-50">
            <h2 class="text-xs font-bold text-pink-600 uppercase">Monthly History</h2>
        </div>
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3">On Hand</th>
                    <th class="px-4 py-3">Missing</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($item->monthlyLogs as $log)
                    <tr>
                        <td class="px-4 py-3">{{ $log->month->format('F Y') }}</td>
                        <td class="px-4 py-3">{{ $log->quantity_on_hand }}</td>
                        <td class="px-4 py-3">{{ $log->missing_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No monthly records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
