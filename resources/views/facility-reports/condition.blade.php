@extends(request()->boolean('embed') ? 'layouts.embed' : 'layouts.shell')

@section('title', 'Condition Report')

@section('content')
    <div class="no-print flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">CONDITION REPORT</h1>
            <p class="text-xs text-gray-400">Facility items grouped by condition</p>
        </div>
        @if (auth()->user()->role === 'executive')
            <div class="flex gap-2">
                <a href="{{ route('executive.reports') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK</a>
                <button onclick="window.print()" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">PRINT REPORT</button>
            </div>
        @endif
    </div>

    <div class="no-print grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Good</div>
            <div class="text-xl font-bold text-green-600">{{ $summary['good'] }}</div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Fair</div>
            <div class="text-xl font-bold text-yellow-600">{{ $summary['fair'] }}</div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Poor</div>
            <div class="text-xl font-bold text-red-600">{{ $summary['poor'] }}</div>
        </div>
    </div>

    <div class="print-full bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        @include('facility-reports.partials.print-header', ['reportTitle' => 'Condition Report'])
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Item Tag</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Condition</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($items as $item)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-mono">{{ $item->item_tag }}</td>
                        <td class="px-4 py-3">{{ $item->name }}</td>
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
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No items to report.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="no-print mt-4">
        @if (method_exists($items, 'links'))
            {{ $items->links() }}
        @endif
    </div>
@endsection
