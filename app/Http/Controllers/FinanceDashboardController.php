<?php

namespace App\Http\Controllers;

use App\Models\FacilityItem;
use App\Models\FacilityMaintenance;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        $totalItems = FacilityItem::count();
        $missingItems = FacilityItem::where('status', 'missing')->count();
        $overdueChecks = FacilityMaintenance::where('status', 'overdue')->count();

        $missingThisMonth = FacilityItem::where('status', 'missing')
            ->whereMonth('missing_since', now()->month)
            ->whereYear('missing_since', now()->year)
            ->count();

        $overdueThisMonth = FacilityMaintenance::where('status', 'overdue')
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->count();

        $discrepanciesThisMonth = $missingThisMonth + $overdueThisMonth;

        $recentMissing = FacilityItem::where('status', 'missing')
            ->latest('missing_since')
            ->take(5)
            ->get();

        return view('finance.dashboard', compact(
            'totalItems', 'missingItems', 'overdueChecks', 'discrepanciesThisMonth', 'recentMissing'
        ));
    }
}
