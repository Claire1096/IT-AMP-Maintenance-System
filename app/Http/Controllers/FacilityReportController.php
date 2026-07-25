<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FacilityItem;
use Illuminate\Http\Request;

class FacilityReportController extends Controller
{
    public function inventory(Request $request)
    {
        $items = FacilityItem::query()
            ->with(['department', 'location'])
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->latest()
            ->get();

        return view('facility-reports.inventory', ['items' => $items]);
    }

    public function condition()
    {
        $items = FacilityItem::with('department')->orderBy('condition')->get();

        $summary = [
            'good' => FacilityItem::where('condition', 'good')->count(),
            'fair' => FacilityItem::where('condition', 'fair')->count(),
            'poor' => FacilityItem::where('condition', 'poor')->count(),
        ];

        return view('facility-reports.condition', ['items' => $items, 'summary' => $summary]);
    }

    public function departmentDistribution()
    {
        $distribution = Department::withCount('facilityItems')->get();

        return view('facility-reports.department-distribution', ['distribution' => $distribution]);
    }

    public function maintenanceDue()
    {
        $maintenances = \App\Models\FacilityMaintenance::with('item')
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();

        return view('facility-reports.maintenance-due', ['maintenances' => $maintenances]);
    }
}