@extends('layouts.shell')

@section('title', 'Edit Asset')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EDIT ASSET</h1>
            <p class="text-xs text-gray-400">{{ $asset->asset_tag }} — {{ $asset->name }}</p>
        </div>
        <a href="{{ route('assets.show', $asset) }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
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

    <form method="POST" action="{{ route('assets.update', $asset) }}" class="max-w-3xl">
        @csrf
        @method('PUT')

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS *</label>
                    <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
                        @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $asset->status) === $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                    <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                    <input type="text" name="model" value="{{ old('model', $asset->model) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                       class="w-full text-xs border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                <textarea name="notes" rows="3" placeholder="Specs, condition, accessories included, or any other details about this asset"
                          class="w-full text-xs border-gray-300 rounded-md">{{ old('notes', $asset->notes) }}</textarea>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Assignment</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                    <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— None —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $asset->department_id) == $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                    <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— None —</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id) == $location->id)>
                                {{ $location->building->name ?? '' }} — {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128722; Procurement</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                    <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration', optional($asset->warranty_expiration)->format('Y-m-d')) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                    <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— None —</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $asset->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                SAVE CHANGES
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Remove this asset? This can be restored by an admin later.')" class="max-w-3xl mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 text-xs hover:underline">Remove this asset</button>
    </form>
@endsection