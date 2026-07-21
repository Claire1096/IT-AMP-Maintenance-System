#!/bin/bash
set -e
mkdir -p resources/views/assets
cat > resources/views/assets/show.blade.php << 'BLADE_EOF'
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $asset->asset_tag }} — {{ $asset->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 bg-gray-200 text-gray-800 text-xs font-semibold rounded-md uppercase">Edit</a>
                <a href="{{ route('maintenance.create', $asset) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md uppercase">Schedule Maintenance</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-3 gap-6">
                {{-- Left: details --}}
                <div class="col-span-2 bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Asset Details</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-gray-500">Category</dt><dd>{{ $asset->category->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Status</dt><dd>{{ ucwords(str_replace('_', ' ', $asset->status)) }}</dd></div>
                        <div><dt class="text-gray-500">Brand / Model</dt><dd>{{ $asset->brand }} {{ $asset->model }}</dd></div>
                        <div><dt class="text-gray-500">Serial Number</dt><dd class="font-mono">{{ $asset->serial_number ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Assigned To</dt><dd>{{ $asset->assignedEmployee->full_name ?? '— unassigned —' }}</dd></div>
                        <div><dt class="text-gray-500">Department</dt><dd>{{ $asset->department->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Location</dt><dd>{{ $asset->location->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Supplier</dt><dd>{{ $asset->supplier->name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Purchase Date</dt><dd>{{ optional($asset->purchase_date)->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Purchase Cost</dt><dd>{{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '—' }}</dd></div>
                        <div><dt class="text-gray-500">Warranty Expiration</dt>
                            <dd class="{{ $asset->isUnderWarranty() ? 'text-green-700' : 'text-red-600' }}">
                                {{ optional($asset->warranty_expiration)->format('M d, Y') ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                    @if ($asset->notes)
                        <div>
                            <dt class="text-gray-500 text-sm">Notes</dt>
                            <dd class="text-sm mt-1">{{ $asset->notes }}</dd>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('assets.reassign', $asset) }}" class="pt-4 border-t flex gap-2 items-end">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Reassign to</label>
                            <select name="employee_id" required class="block w-full border-gray-300 rounded-md text-sm">
                                <option value="">Select employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md uppercase">
                            Reassign
                        </button>
                    </form>
                </div>

                {{-- Right: QR code --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Asset QR Code</h3>
                    @if ($asset->qr_code_path)
                        <img src="{{ Storage::url($asset->qr_code_path) }}" alt="QR code for {{ $asset->asset_tag }}" class="mx-auto w-40 h-40">
                    @else
                        <p class="text-gray-400 text-sm">No QR code generated.</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Scan to view this asset</p>
                </div>
            </div>

            {{-- Maintenance schedules --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Preventive Maintenance</h3>
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase">
                            <th class="py-2">Type</th>
                            <th>Frequency</th>
                            <th>Next Date</th>
                            <th>Status</th>
                            <th>Technician</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
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
                                            <button class="text-indigo-600 text-xs hover:underline">Mark Complete</button>
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

            {{-- Repair history --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Repair History</h3>
                    <form method="POST" action="{{ route('repairs.store', $asset) }}" class="hidden" id="quick-repair-form">
                        @csrf
                    </form>
                </div>
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase">
                            <th class="py-2">Reported</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Cost</th>
                            <th>Downtime (hrs)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
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

            {{-- Assignment & movement history --}}
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Assignment History</h3>
                    <ul class="text-sm space-y-2">
                        @forelse ($asset->assignments as $assignment)
                            <li class="border-b pb-2">
                                {{ $assignment->employee->full_name ?? '—' }}
                                <span class="text-gray-400 text-xs block">
                                    {{ $assignment->assigned_date->format('M d, Y') }}
                                    @if ($assignment->returned_date) – {{ $assignment->returned_date->format('M d, Y') }} @endif
                                </span>
                            </li>
                        @empty
                            <li class="text-gray-400">No assignment history.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Movement History</h3>
                    <ul class="text-sm space-y-2">
                        @forelse ($asset->movements as $movement)
                            <li class="border-b pb-2">
                                {{ $movement->fromLocation->name ?? 'Unknown' }} → {{ $movement->toLocation->name ?? 'Unknown' }}
                                <span class="text-gray-400 text-xs block">{{ $movement->moved_at->format('M d, Y g:i A') }}</span>
                            </li>
                        @empty
                            <li class="text-gray-400">No movement history.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
BLADE_EOF
echo 'show.blade.php created!'
