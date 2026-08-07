@extends('layouts.shell')
@section('title', 'Audit Reports')
@section('content')
    <div class="mb-6">
        <h1 class="text-lg font-bold">AUDIT REPORTS</h1>
        <p class="text-xs text-gray-400">Running total of assets, adjusted each month by items found missing</p>
    </div>

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3">Original Quantity</th>
                    <th class="px-4 py-3">Missing Items (This Month)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($summary as $row)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-semibold">{{ $row['label'] }}</td>
                        <td class="px-4 py-3">{{ $row['original_quantity'] }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full font-semibold text-[10px]',
                                'bg-red-500 text-white' => $row['missing'] > 0,
                                'bg-gray-300 text-gray-700' => $row['missing'] === 0,
                            ])>
                                {{ $row['missing'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">No monthly audit data recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
