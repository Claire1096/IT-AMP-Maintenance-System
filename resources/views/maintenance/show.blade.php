@extends('layouts.shell')
@section('title', 'Maintenance Details')
@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MAINTENANCE DETAILS</h1>
            <p class="text-xs text-gray-400">Maintenance / View Details</p>
        </div>
        <a href="{{ route('maintenance.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
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
                    @if ($schedule->asset)
                        <a href="{{ route('assets.show', $schedule->asset) }}" class="text-pink-600 hover:underline">
                            {{ $schedule->asset->asset_tag }} — {{ $schedule->asset->name }}
                        </a>
                    @else
                        <span class="text-gray-400">Asset removed</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Maintenance Type</div>
                    <div class="text-xs text-gray-700">{{ $schedule->maintenance_type ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Frequency</div>
                    <div class="text-xs text-gray-700">{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Scheduled Date</div>
                    <div class="text-xs text-gray-700">{{ optional($schedule->scheduled_date)->format('M d, Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Next Maintenance Date</div>
                    <div class="text-xs text-gray-700">{{ optional($schedule->next_maintenance_date)->format('M d, Y') ?? '—' }}</div>
                </div>
            </div>

            <div class="mb-3">
                <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Status</div>
                <span @class([
                    'px-2 py-1 rounded-full font-semibold text-[10px]',
                    'bg-blue-500 text-white' => $schedule->status === 'scheduled',
                    'bg-indigo-500 text-white' => $schedule->status === 'in_progress',
                    'bg-green-500 text-white' => $schedule->status === 'completed',
                    'bg-red-500 text-white' => $schedule->status === 'overdue',
                    'bg-gray-400 text-white' => $schedule->status === 'skipped',
                ])>
                    {{ strtoupper(str_replace('_', ' ', $schedule->status)) }}
                </span>
            </div>

            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Technician Remarks</div>
                <div class="text-xs text-gray-700">{{ $schedule->technician_remarks ?: '—' }}</div>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Tech Assignment</h2>
                <div class="mb-4">
                    <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Technician</div>
                    <div class="text-xs text-gray-700">{{ $schedule->technician->name ?? '—' }}</div>
                </div>
                <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#9989; Checklist</h3>
                <div class="space-y-2 mb-3">
                    @forelse ($schedule->checklistItems as $item)
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="{{ $item->is_completed ? 'text-green-600' : 'text-gray-300' }}">&#10003;</span>
                            {{ $item->task_description }}
                        </div>
                    @empty
                        <div class="text-xs text-gray-400">No checklist items recorded.</div>
                    @endforelse
                </div>
                @if ($schedule->completed_at)
                    <div class="mt-4">
                        <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Completed On</div>
                        <div class="text-xs text-gray-700">{{ $schedule->completed_at->format('M d, Y') }}</div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-2 mt-4">
                @if (in_array($schedule->status, ['scheduled', 'overdue', 'in_progress']))
                    <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            MARK COMPLETE
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('maintenance.destroy', $schedule) }}" onsubmit="return confirm('Remove this maintenance schedule?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 border border-red-300 text-red-600 text-xs font-semibold rounded-full hover:bg-red-50">
                        DELETE
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
