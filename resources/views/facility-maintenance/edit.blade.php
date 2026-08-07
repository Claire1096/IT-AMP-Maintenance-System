@extends('layouts.shell')

@section('title', 'Edit Maintenance')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EDIT MAINTENANCE</h1>
            <p class="text-xs text-gray-400">Maintenance / Edit schedule</p>
        </div>
        <a href="{{ route('facility-maintenance.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-3xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility-maintenance.update', $maintenance) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-6 max-w-3xl">

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Maintenance Details</h2>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET *</label>
                    <select name="facility_item_id" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Select Asset</option>
                        @foreach ($items as $facilityItem)
                            <option value="{{ $facilityItem->id }}" @selected(old('facility_item_id', $maintenance->facility_item_id) == $facilityItem->id)>
                                {{ $facilityItem->item_tag }} — {{ $facilityItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">MAINTENANCE TYPE *</label>
                    <select name="maintenance_type" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Select Maintenance Type</option>
                        @foreach ($maintenanceTypes as $type)
                            <option value="{{ $type }}" @selected(old('maintenance_type', $maintenance->maintenance_type) === $type)>{{ ucfirst($type) }} Maintenance</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">SCHEDULE DATE *</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $maintenance->due_date?->format('Y-m-d')) }}" required
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">TIME</label>
                        <input type="time" name="scheduled_time" value="{{ old('scheduled_time', $maintenance->scheduled_time) }}"
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PRIORITY *</label>
                    <select name="priority" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Select Priority Level</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $maintenance->priority) === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION / REMARKS</label>
                    <textarea name="notes" rows="3" placeholder="Add description / remarks / notes..."
                              class="w-full text-xs border-gray-300 rounded-md">{{ old('notes', $maintenance->notes) }}</textarea>
                </div>
            </div>

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Tech Assignment</h2>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">TECHNICIAN</label>
                        <input type="text" name="technician" value="{{ old('technician', $maintenance->technician) }}" placeholder="Enter technician name..."
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>

                    <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#9989; Checklist</h3>

                    <div class="space-y-2 mb-3">
                        @foreach ($checklistItems as $item)
                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" name="checklist[]" value="{{ $item }}"
                                       @checked(collect(old('checklist', $maintenance->checklist ?? []))->contains($item))>
                                {{ $item }}
                            </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="checklist[]" value="Others"
                                   @checked(collect(old('checklist', $maintenance->checklist ?? []))->contains('Others'))>
                            Others
                        </label>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                        SAVE CHANGES
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
