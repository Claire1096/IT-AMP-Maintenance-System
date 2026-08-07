<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-full { grid-column: 1 / -1 !important; width: 100% !important; }
            body { background: white !important; font-size: 9px !important; }
            table { font-size: 9px !important; }
            table th, table td { padding: 2px 4px !important; line-height: 1.2 !important; }
            .rounded-xl, .rounded-full, .rounded-lg, .rounded-md { border-radius: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            .p-8, .p-6, .p-5 { padding: 8px !important; }
            .mb-6, .mb-4 { margin-bottom: 6px !important; }
            @page { size: landscape; margin: 8mm; }
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — EM Power Beautiful Skin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head-scripts')
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="no-print bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
    @php
    $homeRoute = match (auth()->user()->role ?? null) {
        'facility_manager' => route('facility-items.index'),
        'finance_supervisor' => route('audit.index'),
        'it' => route('dashboard'),
        'executive' => route('executive.dashboard'),
        default => url('/'),
    };
@endphp
<a href="{{ $homeRoute }}" class="flex items-center gap-2">
    <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-10 h-10 rounded-full object-cover">
    <div class="leading-tight">
        <div class="text-xl font-bold text-gray-800">E<span class="text-pink-600">M</span> Power Beautiful Skin Corporation</div>
        <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
    </div>
</a>
        <div class="flex items-center gap-4">
           <div class="relative" id="notification-wrapper">
    <button type="button" id="notification-bell" class="relative text-gray-400 hover:text-gray-600">
        &#128276;
        <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
    </button>
    <div id="notification-dropdown"
         class="absolute mt-2 w-80 right-0 bg-white border border-gray-200 rounded-xl shadow-lg hidden z-50 max-h-96 overflow-y-auto text-xs">
    </div>
</div>
           <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="no-print w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
    @php $role = auth()->user()->role ?? null; @endphp

    @if (in_array($role, ['it']))
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
        <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('reports.*') && !request()->routeIs('reports.damage.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
            <span>&#128196;</span> Reports
        </a>
    @endif

    @if (in_array($role, ['facility_manager']))
    <a href="{{ route('facility-items.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-items.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#127970;</span> Facility Inventory
    </a>
    <a href="{{ route('facility-maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-maintenance.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#9881;</span> Maintenance
    </a>
    <a href="{{ route('reports.damage.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('reports.damage.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128196;</span> Reports
    </a>
@endif

@if (in_array($role, ['finance_supervisor']))
    <a href="{{ route('audit.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('audit.index') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128269;</span> Audit
    </a>
    <a href="{{ route('audit-reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('audit-reports.index') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128196;</span> Audit Reports
    </a>
    <a href="{{ route('finance-counts.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('finance-counts.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128203;</span> Monthly Counts
    </a>
@endif

@if (in_array($role, ['executive']))
    <a href="{{ route('executive.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('executive.dashboard') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#8962;</span> Dashboard
    </a>
    <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('assets.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#128421;</span> IT Assets
    </a>
    <a href="{{ route('facility-items.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-items.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#127970;</span> Facility Assets
    </a>
    <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('maintenance.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#9881;</span> IT Maintenance
    </a>
    <a href="{{ route('facility-maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('facility-maintenance.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
        <span>&#9881;</span> Facility Maintenance
    </a>
    <a href="{{ route('executive.reports') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('executive.reports') || request()->routeIs('reports.*') || request()->routeIs('facility-reports.*') ? 'bg-orange-200 text-gray-800' : 'text-gray-600 hover:bg-rose-100' }}">
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