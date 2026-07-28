@extends('layouts.shell')

@section('title', 'Maintenance Details')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MAINTENANCE DETAILS</h1>
            <p class="text-xs text-gray-400">Maintenance / View Details</p>
        </div>
        <a href="{{ route('facility-maintenance.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Maintenance Details</h2>

            <div class="mb-3">
                <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Asset</div>
                <div class="text-xs text-gray-700">
                    @if ($maintenance->item)
                        <a href="{{ route('facility-items.show', $maintenance->item) }}" class="text-pink-600 hover:underline">
                            {{ $maintenance->item->item_tag }} — {{ $maintenance->item->name }}
                        </a>
                    @else
                        <span class="text-gray-400">Item removed</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Maintenance Type</div>
                    <div class="text-xs text-gray-700">{{ $maintenance->maintenance_type ? ucfirst($maintenance->maintenance_type) . ' Maintenance' : '—' }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Priority</div>
                    <div class="text-xs text-gray-700">{{ $maintenance->priority ? ucfirst($maintenance->priority) : '—' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Schedule Date</div>
                    <div class="text-xs text-gray-700">{{ $maintenance->due_date?->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Time</div>
                    <div class="text-xs text-gray-700">{{ $maintenance->scheduled_time ?? '—' }}</div>
                </div>
            </div>

            <div class="mb-3">
                <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Status</div>
                <span @class([
                    'px-2 py-1 rounded-full font-semibold text-[10px]',
                    'bg-yellow-100 text-yellow-700' => $maintenance->status === 'pending',
                    'bg-red-100 text-red-700' => $maintenance->status === 'overdue',
                    'bg-green-100 text-green-700' => $maintenance->status === 'done',
                ])>
                    {{ strtoupper($maintenance->status) }}
                </span>
            </div>

            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Description / Remarks</div>
                <div class="text-xs text-gray-700">{{ $maintenance->notes ?: '—' }}</div>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Tech Assignment</h2>

                <div class="mb-4">
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Technician</div>
                    <div class="text-xs text-gray-700">{{ $maintenance->technician ?: '—' }}</div>
                </div>

                <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#9989; Checklist</h3>

                <div class="space-y-2 mb-3">
                    @forelse ($maintenance->checklist ?? [] as $checkItem)
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="text-green-600">&#10003;</span> {{ $checkItem }}
                        </div>
                    @empty
                        <div class="text-xs text-gray-400">No checklist items recorded.</div>
                    @endforelse
                </div>

                @if ($maintenance->completed_date)
                    <div class="mt-4">
                        <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Completed On</div>
                        <div class="text-xs text-gray-700">{{ $maintenance->completed_date->format('M d, Y') }}</div>
                    </div>
                @endif
            </div>

            @if ($maintenance->status !== 'done')
                <div class="text-right mt-4">
                    <form method="POST" action="{{ route('facility-maintenance.complete', $maintenance) }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            MARK COMPLETE
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection