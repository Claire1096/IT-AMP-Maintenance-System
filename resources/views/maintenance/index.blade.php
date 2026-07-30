@extends('layouts.shell')
@section('title', 'Maintenance')
@section('content')

            <div class="mb-6">
                <h1 class="text-lg font-bold">PREVENTIVE MAINTENANCE</h1>
                <p class="text-xs text-gray-400">All scheduled maintenance</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            @foreach (['scheduled', 'in_progress', 'completed', 'overdue', 'skipped'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" name="due_this_month" value="1" @checked(request('due_this_month'))>
                            Due this month only
                        </label>
                    </div>
                    <div class="flex items-end justify-end gap-2">
                        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR</a>
                        <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">FILTER</button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
                <table class="min-w-full divide-y divide-rose-100 text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Frequency</th>
                            <th class="px-4 py-3">Next Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Technician</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-rose-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('assets.show', $schedule->asset) }}" class="text-pink-600 hover:underline font-mono">
                                        {{ $schedule->asset->asset_tag }}
                                    </a>
                                    <div class="text-gray-400">{{ $schedule->asset->name }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $schedule->maintenance_type }}</td>
                                <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                <td class="px-4 py-3">{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
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
                                </td>
                                <td class="px-4 py-3">{{ $schedule->technician->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @unless (auth()->user()->role === 'executive')
                                        @if (in_array($schedule->status, ['scheduled', 'overdue', 'in_progress']))
                                            <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                                                @csrf
                                                <button class="text-pink-600 hover:underline">Mark Complete</button>
                                            </form>
                                        @endif
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    No maintenance scheduled yet. Go to an asset's page and click "Schedule Maintenance".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $schedules->withQueryString()->links() }}
            </div>

@endsection
