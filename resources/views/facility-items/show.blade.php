@extends('layouts.shell')

@section('title', $item->item_tag)

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">{{ $item->item_tag }} — {{ $item->name }}</h1>
            <p class="text-xs text-gray-400">Facility Inventory / Item details</p>
        </div>
       <div class="flex gap-2">
        <a href="{{ route('facility-items.edit', $item) }}" class="px-4 py-1.5 bg-gray-200 text-gray-800 text-xs font-semibold rounded-full">EDIT</a>
        <a href="{{ route('facility-maintenance.create', ['item' => $item->id]) }}" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-full">SCHEDULE MAINTENANCE</a>
        <a href="{{ route('facility-items.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD NEW</a>
    <a href="{{ route('facility-items.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
</div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-3xl">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-3xl mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#127970; Item Details</h2>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords(str_replace('_', ' ', $item->category)) }}</div>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">QUANTITY</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $item->quantity }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CONDITION</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords($item->condition) }}</div>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords(str_replace('_', ' ', $item->status)) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $item->department->name ?? '—' }}</div>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $item->location->name ?? '—' }}</div>
            </div>
        </div>

        @if ($item->description)
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                <p class="text-xs">{{ $item->description }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-3xl">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128722; Purchase Details</h2>
        <div class="grid grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                {{ $item->supplier->name ?? '—' }}
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                {{ optional($item->purchase_date)->format('M d, Y') ?? '—' }}
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                {{ $item->purchase_cost ? number_format($item->purchase_cost, 2) : '—' }}
            </div>
        </div>
    </div>
@endsection