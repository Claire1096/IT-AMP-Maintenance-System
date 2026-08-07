@extends('layouts.shell')

@section('title', 'Monthly Counts')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MONTHLY COUNTS</h1>
            <p class="text-xs text-gray-400">Physical audit history for finance items</p>
        </div>
        <form method="POST" action="{{ route('finance-counts.create') }}">
            @csrf
            <button type="submit" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ NEW MONTHLY COUNT</button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Items</th>
                    <th class="px-4 py-3">Started By</th>
                    <th class="px-4 py-3">Closed At</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($counts as $count)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-semibold">{{ $count->month->format('F Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full font-semibold text-[10px]',
                                'bg-blue-500 text-white' => $count->status === 'open',
                                'bg-green-500 text-white' => $count->status === 'closed',
                            ])>
                                {{ strtoupper($count->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $count->items_count }}</td>
                        <td class="px-4 py-3">{{ $count->creator->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $count->closed_at?->format('M d, Y g:i A') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('finance-counts.show', $count) }}" class="px-3 py-1 border border-gray-300 text-gray-600 text-[10px] font-semibold rounded-full hover:bg-gray-50">
                                    {{ $count->status === 'open' ? 'CONTINUE' : 'VIEW' }}
                                </a>
                                @if ($count->status === 'closed')
                                    <form method="POST" action="{{ route('finance-counts.reopen', $count) }}" class="inline" onsubmit="return confirm('Reopen this count for editing? Any other open count must be closed first.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 border border-blue-200 text-blue-600 text-[10px] font-semibold rounded-full hover:bg-blue-50">EDIT</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('finance-counts.destroy', $count) }}" class="inline" onsubmit="return confirm('Delete this monthly count? This only removes the count record itself. It will NOT undo any changes already applied to your finance items if this count was closed. Continue?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 border border-red-200 text-red-600 text-[10px] font-semibold rounded-full hover:bg-red-50">DELETE</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No monthly counts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $counts->links() }}
    </div>
@endsection
