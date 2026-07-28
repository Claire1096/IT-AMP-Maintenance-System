@extends('layouts.shell')

@section('title', 'Damage Reports')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">DAMAGE REPORTS</h1>
            <p class="text-xs text-gray-400">Reports / Asset Damage Reports</p>
        </div>
        <a href="{{ route('reports.damage.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ NEW REPORT</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Report #</th>
                    <th class="px-4 py-3">Date Reported</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Asset</th>
                    <th class="px-4 py-3">Cause</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($reports as $report)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3 font-mono">{{ $report->report_number }}</td>
                        <td class="px-4 py-3">{{ $report->date_reported?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $report->category }}</td>
                        <td class="px-4 py-3">{{ $report->asset_name ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $report->cause_of_damage ? ucwords(str_replace('_', ' ', $report->cause_of_damage)) : '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('reports.damage.show', $report) }}" class="text-pink-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            No damage reports yet. <a href="{{ route('reports.damage.create') }}" class="text-pink-600 hover:underline">Create the first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
@endsection