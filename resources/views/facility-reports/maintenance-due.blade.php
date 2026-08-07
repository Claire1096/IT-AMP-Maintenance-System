@extends(request()->boolean('embed') ? 'layouts.embed' : 'layouts.shell')

@section('title', 'Maintenance Due Report')

@section('content')
    <div class="no-print flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MAINTENANCE DUE REPORT</h1>
            <p class="text-xs text-gray-400">Pending and overdue facility maintenance</p>
        </div>
        @if (auth()->user()->role === 'executive')
            <div class="flex gap-2">
                <a href="{{ route('executive.reports') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK</a>
                <button onclick="window.print()" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">PRINT REPORT</button>
            </div>
        @endif
    </div>

    <div class="print-full bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        @include('facility-reports.partials.print-header', ['reportTitle' => 'Maintenance Due Report'])
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3">Due Date</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($maintenances as $maintenance)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3">{{ $maintenance->item->item_tag }} — {{ $maintenance->item->name }}</td>
                        <td class="px-4 py-3">{{ $maintenance->due_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full font-semibold text-[10px]',
                                'bg-yellow-500 text-white' => $maintenance->status === 'pending',
                                'bg-red-500 text-white' => $maintenance->status === 'overdue',
                            ])>
                                {{ strtoupper($maintenance->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Nothing due.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
