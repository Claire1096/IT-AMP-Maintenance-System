@extends('layouts.shell')

@section('title', 'Edit Maintenance Schedule')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EDIT MAINTENANCE SCHEDULE</h1>
            <p class="text-xs text-gray-400">{{ $schedule->asset->asset_tag }} — {{ $schedule->asset->name }}</p>
        </div>
        <a href="{{ route('maintenance.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-2xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('maintenance.update', $schedule) }}" class="max-w-2xl">
        @csrf
        @method('PUT')
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">MAINTENANCE TYPE *</label>
                    <input type="text" name="maintenance_type" value="{{ old('maintenance_type', $schedule->maintenance_type) }}" required
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">FREQUENCY *</label>
                    <select name="frequency" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="one_time" @selected(old('frequency', $schedule->frequency) === 'one_time')>One-time</option>
                        <option value="monthly" @selected(old('frequency', $schedule->frequency) === 'monthly')>Monthly</option>
                        <option value="quarterly" @selected(old('frequency', $schedule->frequency) === 'quarterly')>Quarterly</option>
                        <option value="semi_annual" @selected(old('frequency', $schedule->frequency) === 'semi_annual')>Semi-Annual</option>
                        <option value="annual" @selected(old('frequency', $schedule->frequency) === 'annual')>Annual</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SCHEDULED DATE *</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', optional($schedule->scheduled_date)->format('Y-m-d')) }}" required
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TECHNICIAN</label>
                    <select name="assigned_technician_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— Unassigned —</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(old('assigned_technician_id', $schedule->assigned_technician_id) == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-2">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CHECKLIST ITEMS</label>

                <div id="existing-checklist-items" class="space-y-2 mb-2">
                    @foreach ($schedule->checklistItems as $item)
                        <div class="flex items-center gap-2">
                            <input type="text" name="checklist_items[{{ $item->id }}][task_description]" value="{{ $item->task_description }}"
                                   class="flex-1 text-xs border-gray-300 rounded-md">
                            @if ($item->is_completed)
                                <span class="text-[10px] text-green-600 font-semibold">DONE</span>
                            @endif
                            <label class="flex items-center gap-1 text-[10px] text-red-500">
                                <input type="checkbox" name="checklist_items[{{ $item->id }}][_delete]" value="1">
                                Remove
                            </label>
                        </div>
                    @endforeach
                </div>

                <div id="new-checklist-items" class="space-y-2 mb-2"></div>
                <button type="button" onclick="addChecklistItem()" class="text-xs text-pink-600 hover:underline">+ Add checklist item</button>
            </div>

            <div class="text-right pt-4">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    SAVE CHANGES
                </button>
            </div>
        </div>
    </form>

    <script>
        function addChecklistItem() {
            const container = document.getElementById('new-checklist-items');
            const row = document.createElement('div');
            row.className = 'flex gap-2';
            row.innerHTML = `
                <input type="text" name="new_checklist[]" placeholder="e.g. Check fan/thermal paste" class="flex-1 text-xs border-gray-300 rounded-md">
                <button type="button" onclick="this.parentElement.remove()" class="text-xs text-red-500">&#10005;</button>
            `;
            container.appendChild(row);
        }
    </script>
@endsection
