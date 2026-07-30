<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\FacilityItem;

class ExecutiveController extends Controller
{
    public function index()
    {
        $totalItAssets = Asset::count();
        $itAssets = Asset::with(['category', 'assignedEmployee', 'department'])
            ->latest()
            ->take(5)
            ->get();

        $totalFacilityAssets = FacilityItem::count();
        $facilityItems = FacilityItem::with(['department', 'location'])
            ->latest()
            ->take(5)
            ->get();

        return view('executive.dashboard', compact(
            'totalItAssets', 'itAssets', 'totalFacilityAssets', 'facilityItems'
        ));
    }
}
