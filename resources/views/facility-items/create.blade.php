@extends('layouts.shell')

@section('title', 'Add Facility Item')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">ADD ASSET</h1>
            <p class="text-xs text-gray-400">Asset Management / Add new assets</p>
        </div>
        <a href="{{ route('facility-items.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
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

    <form method="POST" action="{{ route('facility-items.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-6 max-w-3xl">

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET ID</label>
                        <input type="text" disabled placeholder="auto-generated after saving"
                               class="w-full text-xs border-gray-200 rounded-md bg-gray-50 text-gray-400">
                    </div>

                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Enter asset name..."
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Enter asset brand..."
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">QUANTITY *</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CONDITION *</label>
                        <select name="condition" required class="w-full text-xs border-gray-300 rounded-md">
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition }}" @selected(old('condition', 'good') === $condition)>{{ ucwords($condition) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">BARCODE / QR CODE</label>
                    <div class="text-xs text-gray-400 border border-dashed border-gray-300 rounded-md px-3 py-2 flex items-center gap-2">
                        <span>&#128241;</span> Generated automatically after saving
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SET STATUS *</label>
                    <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="in_use" @selected(old('status', 'in_use') === 'in_use')>Active</option>
                        <option value="in_storage" @selected(old('status') === 'in_storage')>In Storage</option>
                        <option value="damaged" @selected(old('status') === 'damaged')>Damaged</option>
                        <option value="disposed" @selected(old('status') === 'disposed')>Disposed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                    <textarea name="description" rows="2" placeholder="Specs, condition notes, or other details"
                              class="w-full text-xs border-gray-300 rounded-md">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Asset Assignment</h2>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY *</label>
                        <input type="text" name="category" list="category-suggestions" value="{{ old('category') }}" required
                               placeholder="Type or pick a category..." autocomplete="off"
                               class="w-full text-xs border-gray-300 rounded-md">
                        <datalist id="category-suggestions">
                            @foreach ($categorySuggestions as $suggestion)
                                <option value="{{ $suggestion }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">BUILDING</label>
                        <select id="building-select" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">Choose Building</option>
                            @foreach ($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                        <select name="location_id" id="location-select" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">Choose building first</option>
                        </select>
                    </div>
                

                    <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#128722; Purchase Details</h3>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                            <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                        <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration') }}"
                               class="w-full text-xs border-gray-300 rounded-md">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    // 1. Pass both 'name' and 'floor' attributes into JS
        const locationsByBuilding = @json(
            $locations->groupBy('building_id')->map(fn ($group) =>
                $group->mapWithKeys(function ($location) {
                    // Combines floor & name if available, e.g. "1st Floor - Corp Store" or just name
                    $label = $location->name ?? ($location->floor ? "Floor {$location->floor}" : 'Ground Floor');
                    if ($location->floor && $location->name) {
                        $label = "{$location->floor} Floor - {$location->name}";
                    }
                return [$location->id => $label];
            })
        )
    );

    const buildingSelect = document.getElementById('building-select');
    const locationSelect = document.getElementById('location-select');

    function populateLocations(buildingId) {
        locationSelect.innerHTML = '';

        const options = locationsByBuilding[buildingId];

        if (!options || Object.keys(options).length === 0) {
            locationSelect.innerHTML = '<option value="">Choose building first</option>';
            return;
        }

        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = 'Choose location';
        locationSelect.appendChild(blank);

        Object.entries(options).forEach(([id, locationLabel]) => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = locationLabel;
            
            // Preserve selected location if form validation reloads (old input)
            if ("{{ old('location_id') }}" == id) {
                opt.selected = true;
            }

            locationSelect.appendChild(opt);
        });
    }

    // Trigger on load (if old input exists)
    if (buildingSelect.value) {
        populateLocations(buildingSelect.value);
    }

    buildingSelect.addEventListener('change', function () {
        populateLocations(this.value);
    });
});
</script>
@endsection