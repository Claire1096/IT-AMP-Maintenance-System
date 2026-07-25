#!/bin/bash
set -e
mkdir -p resources/views/components

echo 'Writing resources/views/components/notification-bell.blade.php'
cat > resources/views/components/notification-bell.blade.php << 'FIXEOF'
@php
    $overdueMaintenance = \App\Models\MaintenanceSchedule::with('asset')
        ->where('status', 'overdue')
        ->orWhere(function ($q) {
            $q->where('status', 'scheduled')->whereDate('next_maintenance_date', '<', now());
        })
        ->limit(10)
        ->get();

    $expiringWarranties = \App\Models\Asset::whereNotNull('warranty_expiration')
        ->whereBetween('warranty_expiration', [now(), now()->addDays(7)])
        ->limit(10)
        ->get();

    $notificationCount = $overdueMaintenance->count() + $expiringWarranties->count();
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false" class="relative text-gray-400 hover:text-gray-600">
        <span class="text-lg">&#128276;</span>
        @if ($notificationCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">
                {{ $notificationCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-rose-200 rounded-xl shadow-lg z-50 text-left">
        <div class="p-3 border-b border-rose-100">
            <h3 class="text-xs font-bold text-gray-700 uppercase">Notifications</h3>
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse ($overdueMaintenance as $schedule)
                <a href="{{ route('assets.show', $schedule->asset) }}" class="block px-3 py-2 border-b border-rose-50 hover:bg-rose-50">
                    <div class="text-xs font-semibold text-red-600">Overdue Maintenance</div>
                    <div class="text-xs text-gray-600">{{ $schedule->asset->asset_tag }} — {{ $schedule->maintenance_type }}</div>
                </a>
            @empty
            @endforelse

            @forelse ($expiringWarranties as $asset)
                <a href="{{ route('assets.show', $asset) }}" class="block px-3 py-2 border-b border-rose-50 hover:bg-rose-50">
                    <div class="text-xs font-semibold text-orange-600">Warranty Expiring Soon</div>
                    <div class="text-xs text-gray-600">{{ $asset->asset_tag }} — expires {{ $asset->warranty_expiration->format('M d, Y') }}</div>
                </a>
            @empty
            @endforelse

            @if ($notificationCount === 0)
                <div class="px-3 py-6 text-center text-xs text-gray-400">No notifications right now.</div>
            @endif
        </div>
    </div>
</div>

FIXEOF
echo 'notification-bell.blade.php fixed!'