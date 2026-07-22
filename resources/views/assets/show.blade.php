@extends('layouts.shell')

@section('title', $asset->asset_tag)

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">{{ $asset->asset_tag }} — {{ $asset->name }}</h1>
            <p class="text-xs text-gray-400">Asset Management / Asset details</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-1.5 bg-gray-200 text-gray-800 text-xs font-semibold rounded-full">EDIT</a>
            <a href="{{ route('maintenance.create', $asset) }}" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-full">SCHEDULE MAINTENANCE</a>
            <a href="{{ route('repairs.create', $asset) }}" class="px-4 py-1.5 bg-yellow-600 text-white text-xs font-semibold rounded-full">LOG REPAIR</a>
            <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-4xl">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-6 max-w-4xl mb-6">

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Details</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                    <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->category->name ?? '—' }}</div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                    <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                    <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->brand }} {{ $asset->model }}</div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                    <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->serial_number ?? '—' }}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->assignedEmployee->full_name ?? 'unassigned' }}</div>
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->department->name ?? 'unassigned' }}</div>
            </div>

            <form method="POST" action="{{ route('assets.reassign', $asset) }}">
                @csrf
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">REASSIGN TO</label>
                <select name="employee_id" required class="w-full text-xs border-gray-300 rounded-md mb-3">
                    <option value="">unassigned</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>
                <div class="text-right">
                    <button type="submit" class="px-5 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                        REASSIGN
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col items-center justify-center text-center">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Asset QR Code</h2>
            @if ($asset->qr_code_path)
                <img src="{{ Storage::url($asset->qr_code_path) }}" alt="QR code" class="w-48 h-48 mb-3">
            @else
                <div class="w-48 h-48 mb-3 flex items-center justify-center text-gray-300 border border-dashed border-gray-300 rounded-md">
                    No QR generated
                </div>
            @endif
            <p class="text-xs font-semibold text-gray-700">ASSET ID: {{ $asset->asset_tag }}</p>
        </div>
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Procurement</h2>
        <div class="grid grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                {{ $asset->supplier->name ?? '—' }}
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                {{ optional($asset->purchase_date)->format('M d, Y') ?? '—' }}
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                {{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '—' }}
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY</label>
                <span class="{{ $asset->isUnderWarranty() ? 'text-green-700' : 'text-red-600' }}">
                    {{ optional($asset->warranty_expiration)->format('M d, Y') ?? '—' }}
                </span>
            </div>
        </div>
        @if ($asset->notes)
            <div class="mt-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                <p class="text-xs">{{ $asset->notes }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Preventive Maintenance</h2>
        <table class="min-w-full text-xs divide-y divide-rose-100">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase">
                    <th class="py-2">Type</th>
                    <th>Frequency</th>
                    <th>Next Date</th>
                    <th>Status</th>
                    <th>Technician</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($asset->maintenanceSchedules as $schedule)
                    <tr>
                        <td class="py-2">{{ $schedule->maintenance_type }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                        <td>{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                        <td>{{ ucwords($schedule->status) }}</td>
                        <td>{{ $schedule->technician->name ?? '—' }}</td>
                        <td>
                            @if (in_array($schedule->status, ['scheduled', 'overdue']))
                                <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                                    @csrf
                                    <button class="text-pink-600 hover:underline">Mark Complete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-400">No maintenance scheduled yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Repair History</h2>
        <table class="min-w-full text-xs divide-y divide-rose-100">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase">
                    <th class="py-2">Reported</th>
                    <th>Issue</th>
                    <th>Status</th>
                    <th>Cost</th>
                    <th>Downtime (hrs)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse ($asset->repairHistories as $repair)
                    <tr>
                        <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                        <td>{{ Str::limit($repair->issue_description, 60) }}</td>
                        <td>{{ ucwords($repair->status) }}</td>
                        <td>{{ number_format($repair->cost, 2) }}</td>
                        <td>{{ $repair->downtime_hours ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-400">No repairs logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-2 gap-6 max-w-4xl">
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Assignment History</h2>
            <ul class="text-xs space-y-2">
                @forelse ($asset->assignments as $assignment)
                    <li class="border-b border-rose-50 pb-2">
                        {{ $assignment->employee->full_name ?? '—' }}
                        <span class="text-gray-400 text-[10px] block">
                            {{ $assignment->assigned_date->format('M d, Y') }}
                            @if ($assignment->returned_date) – {{ $assignment->returned_date->format('M d, Y') }} @endif
                        </span>
                    </li>
                @empty
                    <li class="text-gray-400">No assignment history.</li>
                @endforelse
            </ul>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Movement History</h2>
            <ul class="text-xs space-y-2">
                @forelse ($asset->movements as $movement)
                    <li class="border-b border-rose-50 pb-2">
                        {{ $movement->fromLocation->name ?? 'Unknown' }} → {{ $movement->toLocation->name ?? 'Unknown' }}
                        <span class="text-gray-400 text-[10px] block">{{ $movement->moved_at->format('M d, Y g:i A') }}</span>
                    </li>
                @empty
                    <li class="text-gray-400">No movement history.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection