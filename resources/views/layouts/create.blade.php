<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold">ADD ASSET</h1>
    </x-slot>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            {{-- your sidebar links --}}
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-xs text-gray-400">Asset Management / Add new asset</p>
                </div>
                <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                {{-- your error block --}}
            @endif

            <form method="POST" action="{{ route('assets.store') }}">
                @csrf
                {{-- rest of your form fields, unchanged --}}
            </form>
        </div>
    </div>
</x-app-layout>