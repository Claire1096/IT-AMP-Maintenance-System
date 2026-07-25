<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2">
        <x-user-avatar :name="auth()->user()->name" />
        <div class="leading-tight text-xs text-left">
            <div class="font-semibold">{{ auth()->user()->name }}</div>
            <div class="text-gray-400">{{ auth()->user()->role ?? 'viewer' }}</div>
        </div>
    </button>
    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-rose-200 rounded-xl shadow-lg z-50 text-left">
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 rounded-t-xl">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-rose-50 rounded-b-xl">Logout</button>
        </form>
    </div>
</div>