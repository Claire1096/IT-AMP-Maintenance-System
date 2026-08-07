@extends(request()->boolean('embed') ? 'layouts.embed' : 'layouts.shell')
@section('title', 'Yearly Facility Summary')
@section('content')
    <div class="no-print flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">YEARLY FACILITY SUMMARY</h1>
            <p class="text-xs text-gray-400">{{ $year }}</p>
        </div>
        <button onclick="window.print()" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">PRINT REPORT</button>
    </div>

    <div class="no-print grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">New Items</div>
            <div class="text-xl font-bold">{{ $newItems }}</div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Maintenance Completed</div>
            <div class="text-xl font-bold">{{ $maintenanceCompleted }}</div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Purchase Spend</div>
            <div class="text-xl font-bold">{{ number_format($totalSpendOnPurchases, 2) }}</div>
        </div>
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-8">
        <div class="flex flex-col items-center text-center mb-6">
            <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-12 h-12 rounded-full object-cover mb-2">
            <div class="font-semibold text-sm">E<span class="text-pink-600">M</span> Power Beautiful Skin</div>
            <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
            <h2 class="font-bold text-base uppercase">Yearly Facility Summary — {{ $year }}</h2>
        </div>

        <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">New Items by Category</h3>
        <table class="min-w-full text-xs border-t border-gray-200">
            <thead>
                <tr class="text-left border-b border-gray-200">
                    <th class="py-2">Category</th>
                    <th class="py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byCategory as $row)
                    <tr class="border-b border-gray-100">
                        <td class="py-2">{{ ucwords(str_replace('_', ' ', $row->category)) }}</td>
                        <td class="py-2">{{ $row->total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-6 text-center text-gray-400">No new items registered this year.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="GET" class="no-print bg-white border border-rose-100 rounded-xl p-5 shadow-sm mt-6 max-w-sm">
        <div class="mb-3">
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">YEAR</label>
            <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="w-full text-xs border-gray-300 rounded-md">
        </div>
        <div class="text-right">
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">FILTER</button>
        </div>
    </form>
@endsection
