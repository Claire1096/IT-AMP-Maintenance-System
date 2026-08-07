<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FacilityItem;
use App\Models\FacilityMaintenance;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FacilityReportController extends Controller
{
    /**
     * Apply the shared Report Period filter (monthly/annual/all) to a date column.
     */
    private function applyPeriod(Builder $query, string $column, Request $request): Builder
    {
        $period = $request->get('period');
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        if ($period === 'monthly') {
            return $query->whereMonth($column, $month)->whereYear($column, $year);
        }

        if ($period === 'annual') {
            return $query->whereYear($column, $year);
        }

        return $query;
    }

    public function inventory(Request $request)
    {
        $query = FacilityItem::query()
            ->with(['department', 'location'])
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->tap(fn ($q) => $this->applyPeriod($q, 'created_at', $request))
            ->latest();

        $items = $request->boolean('print')
            ? $query->get()
            : $query->paginate(12)->withQueryString();

        return view('facility-reports.inventory', ['items' => $items]);
    }

    public function condition(Request $request)
    {
        $query = FacilityItem::with('department')
            ->tap(fn ($q) => $this->applyPeriod($q, 'created_at', $request))
            ->orderBy('condition');

        $items = $request->boolean('print')
            ? $query->get()
            : $query->paginate(12)->withQueryString();

        $summary = [
            'good' => FacilityItem::where('condition', 'good')->count(),
            'fair' => FacilityItem::where('condition', 'fair')->count(),
            'poor' => FacilityItem::where('condition', 'poor')->count(),
        ];

        return view('facility-reports.condition', ['items' => $items, 'summary' => $summary]);
    }

    public function departmentDistribution(Request $request)
    {
        $distribution = Department::withCount(['facilityItems' => function ($q) use ($request) {
            $this->applyPeriod($q, 'created_at', $request);
        }])->get();

        return view('facility-reports.department-distribution', ['distribution' => $distribution]);
    }

    public function maintenanceDue(Request $request)
    {
        $maintenances = FacilityMaintenance::with('item')
            ->whereIn('status', ['pending', 'overdue'])
            ->tap(fn ($q) => $this->applyPeriod($q, 'due_date', $request))
            ->orderBy('due_date')
            ->get();

        return view('facility-reports.maintenance-due', ['maintenances' => $maintenances]);
    }

    public function monthlySummary(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $newItems = FacilityItem::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $maintenanceCompleted = FacilityMaintenance::whereMonth('completed_date', $month)
            ->whereYear('completed_date', $year)
            ->where('status', 'done')
            ->count();
        $totalSpendOnPurchases = FacilityItem::whereMonth('purchase_date', $month)
            ->whereYear('purchase_date', $year)
            ->sum('purchase_cost');

        $byCategory = FacilityItem::selectRaw('category, COUNT(*) as total')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('category')
            ->get();

        return view('facility-reports.monthly-summary', compact(
            'month', 'year', 'newItems', 'maintenanceCompleted', 'totalSpendOnPurchases', 'byCategory'
        ));
    }

    public function yearlySummary(Request $request)
    {
        $year = $request->integer('year', now()->year);

        $newItems = FacilityItem::whereYear('created_at', $year)->count();
        $maintenanceCompleted = FacilityMaintenance::whereYear('completed_date', $year)
            ->where('status', 'done')
            ->count();
        $totalSpendOnPurchases = FacilityItem::whereYear('purchase_date', $year)->sum('purchase_cost');

        $byCategory = FacilityItem::selectRaw('category, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('category')
            ->get();

        return view('facility-reports.yearly-summary', compact(
            'year', 'newItems', 'maintenanceCompleted', 'totalSpendOnPurchases', 'byCategory'
        ));
    }
}
