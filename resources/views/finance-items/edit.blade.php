@extends('layouts.shell')

@section('title', 'Edit Finance Item')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">EDIT ITEM</h1>
            <p class="text-xs text-gray-400">{{ $item->item_tag }} — {{ $item->name }}</p>
        </div>
        <a href="{{ route('audit.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
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

    <form method="POST" action="{{ route('finance-items.update', $item) }}" class="max-w-2xl">
        @csrf
        @method('PUT')
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET TYPE</label>
                    <select name="asset_type" class="w-full text-xs border-gray-300 rounded-md">
                        <option value="">Choose Asset Type</option>
                        @foreach ($assetTypes as $type)
                            <option value="{{ $type }}" @selected(old('asset_type', $item->asset_type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">TOTAL QUANTITY *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $item->quantity) }}" min="0" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">CURRENT QUANTITY *</label>
                    <input type="number" name="current_quantity" value="{{ old('current_quantity', $item->current_quantity) }}" min="0" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id', $item->department_id) == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS *</label>
                <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
                    <option value="in_use" @selected(old('status', $item->status) === 'in_use')>In Use</option>
                    <option value="in_storage" @selected(old('status', $item->status) === 'in_storage')>In Storage</option>
                    <option value="damaged" @selected(old('status', $item->status) === 'damaged')>Damaged</option>
                    <option value="disposed" @selected(old('status', $item->status) === 'disposed')>Disposed</option>
                    <option value="missing" @selected(old('status', $item->status) === 'missing')>Missing</option>
                </select>
            </div>

            <div class="text-right pt-2">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    SAVE CHANGES
                </button>
            </div>
        </div>
    </form>
@endsection
