<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Department;
use App\Models\MaintenanceSchedule;
use App\Models\RepairHistory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $underRepair = Asset::where('status', 'under_repair')->count();
        $forDisposal = Asset::where('status', 'for_disposal')->count();
        $lost = Asset::where('status', 'lost')->count();

        $warrantyExpiringSoon = Asset::whereNotNull('warranty_expiration')
            ->whereBetween('warranty_expiration', [now(), now()->addDays(30)])
            ->count();
        $warrantyExpired = Asset::whereNotNull('warranty_expiration')
            ->where('warranty_expiration', '<', now())
            ->count();

        $maintenanceDueThisMonth = MaintenanceSchedule::whereIn('status', ['scheduled', 'overdue'])
            ->whereMonth('next_maintenance_date', now()->month)
            ->whereYear('next_maintenance_date', now()->year)
            ->count();

        $assetsByDepartment = Department::withCount('assets')->get(['id', 'name']);

        // Monthly maintenance report: completed schedules + repair costs, grouped by month, last 6 months
        $monthlyMaintenance = MaintenanceSchedule::selectRaw("strftime('%Y-%m', completed_at) as month, COUNT(*) as completed_count")
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRepairCost = RepairHistory::selectRaw("strftime('%Y-%m', repair_date) as month, SUM(cost) as total_cost")
            ->whereNotNull('repair_date')
            ->where('repair_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $assetsByCategory = Asset::selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get();

        return view('dashboard', compact(
            'totalAssets', 'activeAssets', 'underRepair', 'forDisposal', 'lost',
            'warrantyExpiringSoon', 'warrantyExpired', 'maintenanceDueThisMonth',
            'assetsByDepartment', 'monthlyMaintenance', 'monthlyRepairCost', 'assetsByCategory'
        ));
    }
}

