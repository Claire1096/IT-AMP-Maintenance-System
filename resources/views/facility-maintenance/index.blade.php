@extends('layouts.shell')

@section('title', 'Facility Maintenance')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MAINTENANCE SCHEDULE</h1>
            <p class="text-xs text-gray-400">Asset Management / Preventive maintenance tracking</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('facility-items.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK TO ASSETS</a>
            <a href="{{ route('facility-maintenance.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ SCHEDULE MAINTENANCE</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-3xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-4 mb-6 max-w-3xl">
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Pending</p>
            <p class="text-2xl font-bold text-gray-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Overdue</p>
            <p class="text-2xl font-bold text-pink-600">{{ $stats['overdue'] }}</p>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Done</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['done'] }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2 mb-4">
        <form method="GET" class="flex items-center gap-2">
            <label class="text-[10px] font-semibold text-gray-500 uppercase">Filter status:</label>
            <select name="status" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-md">
                <option value="">All</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                <option value="done" @selected(request('status') === 'done')>Done</option>
            </select>
        </form>
    </div>

    <div class="bg-white border border-rose-100 rounded-xl shadow-sm overflow-hidden max-w-4xl">
        <table class="w-full text-xs">
            <thead class="bg-rose-50 text-pink-600 uppercase text-[10px]">
                <tr>
                    <th class="text-left px-4 py-2">Asset</th>
                    <th class="text-left px-4 py-2">Due Date</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-left px-4 py-2">Notes</th>
                    <th class="text-right px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($maintenances as $maintenance)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($maintenance->item)
                                <a href="{{ route('facility-items.show', $maintenance->item) }}" class="text-pink-600 font-semibold hover:underline">
                                    {{ $maintenance->item->name }}
                                </a>
                            @else
                                <span class="text-gray-400">Item removed</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $maintenance->due_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase',
                                'bg-yellow-100 text-yellow-700' => $maintenance->status === 'pending',
                                'bg-red-100 text-red-700' => $maintenance->status === 'overdue',
                                'bg-green-100 text-green-700' => $maintenance->status === 'done',
                            ])>
                                {{ $maintenance->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $maintenance->notes ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($maintenance->status !== 'done')
                                <form method="POST" action="{{ route('facility-maintenance.complete', $maintenance) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 bg-pink-600 text-white text-[10px] font-semibold rounded-full hover:bg-pink-700">
                                        MARK DONE
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No maintenance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 max-w-4xl">
        {{ $maintenances->links() }}
    </div>
@endsection