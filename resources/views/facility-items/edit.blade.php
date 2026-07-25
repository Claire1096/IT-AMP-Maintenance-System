@extends('layouts.shell')

@section('title', 'Edit Facility Item')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EDIT FACILITY ITEM</h1>
            <p class="text-xs text-gray-400">{{ $item->item_tag }} — {{ $item->name }}</p>
        </div>
        <a href="{{ route('facility-items.show', $item) }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
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

    <form method="POST" action="{{ route('facility-items.update', $item) }}" class="max-w-3xl">
        @csrf
        @method('PUT')

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#127970; Item Information</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ITEM NAME *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY *</label>
                    <select name="category" required class="w-full text-xs border-gray-300 rounded-md">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(old('category', $item->category) === $category)>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">QUANTITY *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $item->quantity) }}" min="1" required
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">CONDITION *</label>
                    <select name="condition" required class="w-full text-xs border-gray-300 rounded-md">
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition }}" @selected(old('condition', $item->condition) === $condition)>{{ ucwords($condition) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS *</label>
                    <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $item->status) === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                <textarea name="description" rows="3"
                          class="w-full text-xs border-gray-300 rounded-md">{{ old('description', $item->description) }}</textarea>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128205; Location & Assignment</h2>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                    <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— None —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $item->department_id) == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                    <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">— None —</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id', $item->location_id) == $location->id)>
                                {{ $location->building->name ?? '' }} — {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128722; Purchase Details</h2>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($item->purchase_date)->format('Y-m-d')) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $item->purchase_cost) }}"
                           class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $item->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                SAVE CHANGES
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('facility-items.destroy', $item) }}" onsubmit="return confirm('Remove this facility item?')" class="max-w-3xl mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 text-xs hover:underline">Remove this item</button>
    </form>
@endsection