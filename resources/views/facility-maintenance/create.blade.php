@extends('layouts.shell')

@section('title', 'Schedule Maintenance')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">SCHEDULE MAINTENANCE</h1>
            <p class="text-xs text-gray-400">{{ $item->item_tag }} &mdash; {{ $item->name }}</p>
        </div>
        <a href="{{ route('facility-items.show', $item) }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-md">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility-maintenance.store', $item) }}" class="max-w-md">
        @csrf
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DUE DATE *</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" required
                       class="w-full text-xs border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">NOTES</label>
                <textarea name="notes" rows="3" class="w-full text-xs border-gray-300 rounded-md">{{ old('notes') }}</textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    SCHEDULE
                </button>
            </div>
        </div>
    </form>
@endsection