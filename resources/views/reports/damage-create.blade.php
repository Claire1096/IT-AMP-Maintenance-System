@extends('layouts.shell')

@section('title', 'Asset Damage Report')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">REPORTS</h1>
            <p class="text-xs text-gray-400">Reports / Asset Damage Report</p>
        </div>
        <a href="{{ route('reports.damage.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reports.damage.store') }}">
        @csrf
        <div class="grid grid-cols-3 gap-6">

            <div class="col-span-2 bg-white border border-rose-100 rounded-xl p-6 shadow-sm">
                <div class="text-center mb-4">
                    <h2 class="text-sm font-bold text-pink-600 uppercase">EM Power Beautiful Skin</h2>
                    <p class="text-xs text-gray-400 uppercase">Corporation</p>
                    <h3 class="text-base font-bold mt-3">ASSET DAMAGE REPORT</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="space-y-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DATE REPORTED *</label>
                            <input type="date" name="date_reported" value="{{ old('date_reported', now()->toDateString()) }}" required class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DATE OF INCIDENT</label>
                            <input type="date" name="date_of_incident" value="{{ old('date_of_incident') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">TIME OF INCIDENT</label>
                            <input type="time" name="time_of_incident" value="{{ old('time_of_incident') }}" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">TYPE OF INCIDENT</label>
                            <input type="text" name="type_of_incident" value="{{ old('type_of_incident') }}" placeholder="e.g. Fire, spill, fall..." class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CAUSE OF DAMAGE</label>
                        <div class="space-y-1 text-xs text-gray-600">
                            @foreach ($causes as $cause)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="cause_of_damage" value="{{ $cause }}" @checked(old('cause_of_damage') === $cause)>
                                    {{ ucwords(str_replace('_', ' ', $cause)) }}
                                </label>
                            @endforeach
                        </div>
                        <input type="text" name="cause_other_note" value="{{ old('cause_other_note') }}" placeholder="If 'Other', specify..." class="w-full text-xs border-gray-300 rounded-md mt-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                    <textarea name="description" rows="3" placeholder="Describe the incident..." class="w-full text-xs border-gray-300 rounded-md">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ACTION TAKEN</label>
                        <select name="action_taken" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">Select</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(old('action_taken') === $action)>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CONDITION</label>
                        <input type="text" name="condition" value="{{ old('condition') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">INSPECTED BY</label>
                        <input type="text" name="inspected_by" value="{{ old('inspected_by') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">INSPECTION DATE</label>
                        <input type="date" name="inspection_date" value="{{ old('inspection_date') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ESTIMATED COST</label>
                        <input type="number" step="0.01" name="estimated_cost" value="{{ old('estimated_cost') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">REMARKS</label>
                    <textarea name="remarks" rows="2" class="w-full text-xs border-gray-300 rounded-md">{{ old('remarks') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">FACILITATOR NAME</label>
                        <input type="text" name="facilitator_name" value="{{ old('facilitator_name') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DATE</label>
                        <input type="date" name="facilitator_date" value="{{ old('facilitator_date') }}" class="w-full text-xs border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                        SAVE
                    </button>
                </div>
            </div>

            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm h-fit">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Report Details</h2>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                    <select id="category-select" name="category" required class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Choose Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME</label>
                    <select id="asset-select" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Select category first</option>
                    </select>
                    <input type="hidden" name="facility_item_id" id="facility-item-id-input">
                    <input type="hidden" name="asset_id" id="asset-id-input">
                    <input type="hidden" name="asset_name" id="asset-name-input">
                    <input type="hidden" name="asset_tag_no" id="asset-tag-input">
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET TYPE</label>
                    <input type="text" id="asset-type-display" disabled placeholder="Auto-filled from asset" class="w-full text-xs border-gray-200 rounded-md bg-gray-50 text-gray-400">
                    <input type="hidden" name="asset_type" id="asset-type-input">
                </div>
            </div>
        </div>
    </form>

    <script>
        const facilityItems = @json($facilityItems);
        const assets = @json($assets);

        const categorySelect = document.getElementById('category-select');
        const assetSelect = document.getElementById('asset-select');
        const assetTypeDisplay = document.getElementById('asset-type-display');

        const facilityItemIdInput = document.getElementById('facility-item-id-input');
        const assetIdInput = document.getElementById('asset-id-input');
        const assetNameInput = document.getElementById('asset-name-input');
        const assetTagInput = document.getElementById('asset-tag-input');
        const assetTypeInput = document.getElementById('asset-type-input');

        function clearHidden() {
            facilityItemIdInput.value = '';
            assetIdInput.value = '';
            assetNameInput.value = '';
            assetTagInput.value = '';
            assetTypeInput.value = '';
            assetTypeDisplay.value = '';
        }

        function populateAssets() {
            assetSelect.innerHTML = '';
            clearHidden();

            const category = categorySelect.value;
            const blank = document.createElement('option');
            blank.value = '';
            blank.textContent = category ? 'Select Asset' : 'Select category first';
            assetSelect.appendChild(blank);

            if (category === 'Facility and Maintenance') {
                facilityItems.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = `${item.item_tag} — ${item.name}`;
                    opt.dataset.tag = item.item_tag;
                    opt.dataset.name = item.name;
                    opt.dataset.type = item.asset_type || '';
                    opt.dataset.kind = 'facility';
                    assetSelect.appendChild(opt);
                });
            } else if (category === 'Fixed Asset Inventory') {
                assets.forEach(asset => {
                    const opt = document.createElement('option');
                    opt.value = asset.id;
                    opt.textContent = `${asset.asset_tag} — ${asset.name}`;
                    opt.dataset.tag = asset.asset_tag;
                    opt.dataset.name = asset.name;
                    opt.dataset.type = asset.category ? asset.category.name : '';
                    opt.dataset.kind = 'asset';
                    assetSelect.appendChild(opt);
                });
            }
        }

        assetSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            if (!selected.value) {
                clearHidden();
                return;
            }

            assetNameInput.value = selected.dataset.name || '';
            assetTagInput.value = selected.dataset.tag || '';
            assetTypeInput.value = selected.dataset.type || '';
            assetTypeDisplay.value = selected.dataset.type || '';

            if (selected.dataset.kind === 'facility') {
                facilityItemIdInput.value = selected.value;
                assetIdInput.value = '';
            } else {
                assetIdInput.value = selected.value;
                facilityItemIdInput.value = '';
            }
        });

        categorySelect.addEventListener('change', populateAssets);

        if (categorySelect.value) {
            populateAssets();
        }
    </script>
@endsection