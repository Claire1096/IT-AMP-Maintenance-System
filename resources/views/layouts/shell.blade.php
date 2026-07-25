<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head-scripts')
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative" id="global-search-wrapper" style="min-width: 260px;">
                <div class="flex items-center border border-blue-500 rounded-full px-4 py-1.5 bg-white">
                    <input
                        type="text"
                        id="global-search-input"
                        placeholder="search asset ID/Employee..."
                        class="outline-none text-xs w-56"
                        autocomplete="off"
                    >
                </div>
                <div id="global-search-results"
                     class="absolute mt-1 w-72 right-0 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-50 max-h-96 overflow-y-auto">
                </div>
            </div>
           <div class="relative" id="notification-wrapper">
    <button type="button" id="notification-bell" class="relative text-gray-400 hover:text-gray-600">
        &#128276;
        <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
    </button>
    <div id="notification-dropdown"
         class="absolute mt-2 w-80 right-0 bg-white border border-gray-200 rounded-xl shadow-lg hidden z-50 max-h-96 overflow-y-auto text-xs">
    </div>
</div>
           <div class="flex items-center gap-2">
    <x-user-avatar :name="auth()->user()->name ?? 'Admin user'" />
    <div class="leading-tight text-xs">
        <div class="font-semibold">{{ auth()->user()->name ?? 'Admin user' }}</div>
        <div class="text-gray-400">{{ auth()->user()->role ?? 'administrator' }}</div>
    </div>
</div>
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
    @php $role = auth()->user()->role ?? null; @endphp

    @if (in_array($role, ['it', 'executive']))
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('dashboard') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#8962;</span> Dashboard
        </a>
        <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('assets.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#128421;</span> Assets
        </a>
        <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('employees.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#128101;</span> Employees
        </a>
        <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('maintenance.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#9881;</span> Maintenance
        </a>
        <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('reports.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#128196;</span> Reports
        </a>
    @endif

    @if (in_array($role, ['facility_manager', 'executive']))
    <a href="{{ route('facility-items.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-items.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#127970;</span> Facility Inventory
    </a>
    <a href="{{ route('facility-maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-maintenance.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#9881;</span> Maintenance
    </a>
    <a href="{{ route('facility-reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-reports.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128196;</span> Reports
    </a>
@endif
</div>
@if (session('error'))
    <div class="bg-red-100 text-red-800 text-xs px-6 py-2">{{ session('error') }}</div>
@endif
        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>