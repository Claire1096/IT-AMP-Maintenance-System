@extends('layouts.shell')

@section('title', 'Department Distribution')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">DEPARTMENT DISTRIBUTION</h1>
            <p class="text-xs text-gray-400">Facility items count per department</p>
        </div>
    </div>

    <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
        <table class="min-w-full divide-y divide-rose-100 text-xs">
            <thead class="bg-pink-100">
                <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Item Count</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($distribution as $dept)
                    <tr class="hover:bg-rose-50">
                        <td class="px-4 py-3">{{ $dept->name }}</td>
                        <td class="px-4 py-3">{{ $dept->facility_items_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-8 text-center text-gray-400">No data to report.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection