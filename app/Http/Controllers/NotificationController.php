<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $warrantyExpiring = Asset::query()
            ->whereNotNull('warranty_expiration')
            ->whereBetween('warranty_expiration', [now(), now()->addDays(30)])
            ->orderBy('warranty_expiration')
            ->limit(10)
            ->get();

        $maintenanceDue = MaintenanceSchedule::query()
            ->with('asset')
            ->whereIn('status', ['scheduled', 'overdue'])
            ->whereDate('next_maintenance_date', '<=', now()->addDays(7))
            ->orderBy('next_maintenance_date')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => $warrantyExpiring->count() + $maintenanceDue->count(),
            'warranty' => $warrantyExpiring->map(fn ($a) => [
                'label' => "{$a->name} ({$a->asset_tag})",
                'detail' => 'Warranty expires ' . $a->warranty_expiration->format('M d, Y'),
                'url' => route('assets.show', $a->id),
            ]),
            'maintenance' => $maintenanceDue->map(fn ($m) => [
                'label' => $m->asset->name ?? 'Unknown asset',
                'detail' => ucfirst($m->status) . ' — due ' . optional($m->next_maintenance_date)->format('M d, Y'),
                'url' => route('assets.show', $m->asset_id),
            ]),
        ]);
    }
}