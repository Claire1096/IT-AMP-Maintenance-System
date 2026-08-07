<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\MaintenanceSchedule;
use App\Models\RepairHistory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
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

    // 1. Asset Inventory Report
    public function inventory(Request $request)
    {
        $assets = Asset::with(['department', 'location', 'assignedEmployee'])
            ->when($request->category, fn ($q) => $q->where('category', 'like', '%' . $request->category . '%'))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->tap(fn ($q) => $this->applyPeriod($q, 'created_at', $request))
            ->get();

        return view('reports.inventory', compact('assets'));
    }

    // 2. Preventive Maintenance Report
    public function preventiveMaintenance(Request $request)
    {
        $schedules = MaintenanceSchedule::with(['asset', 'technician'])
            ->when($request->from, fn ($q) => $q->whereDate('scheduled_date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scheduled_date', '<=', $request->to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->tap(fn ($q) => $this->applyPeriod($q, 'scheduled_date', $request))
            ->orderBy('scheduled_date')
            ->get();

        return view('reports.preventive-maintenance', compact('schedules'));
    }

    // 3. Warranty Expiration Report
    public function warrantyExpiration(Request $request)
    {
        $withinDays = $request->integer('within_days', 90);

        $assets = Asset::with(['department'])
            ->whereNotNull('warranty_expiration')
            ->when($request->get('period') === 'monthly' || $request->get('period') === 'annual', function ($q) use ($request) {
                $this->applyPeriod($q, 'warranty_expiration', $request);
            }, function ($q) use ($withinDays) {
                $q->whereDate('warranty_expiration', '<=', now()->addDays($withinDays));
            })
            ->orderBy('warranty_expiration')
            ->get();

        return view('reports.warranty-expiration', compact('assets', 'withinDays'));
    }

    // 4. Repair History Report
    public function repairHistory(Request $request)
    {
        $repairs = RepairHistory::with(['asset', 'technician', 'parts'])
            ->when($request->from, fn ($q) => $q->whereDate('reported_date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('reported_date', '<=', $request->to))
            ->when($request->asset_id, fn ($q) => $q->where('asset_id', $request->asset_id))
            ->tap(fn ($q) => $this->applyPeriod($q, 'reported_date', $request))
            ->orderByDesc('reported_date')
            ->get();

        $totalCost = $repairs->sum('cost');

        return view('reports.repair-history', compact('repairs', 'totalCost'));
    }

    // 5. Asset Assignment Report
    public function assetAssignment(Request $request)
    {
        $assignments = AssetAssignment::with(['asset', 'employee', 'department', 'assignedBy'])
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->active_only, fn ($q) => $q->whereNull('returned_date'))
            ->tap(fn ($q) => $this->applyPeriod($q, 'assigned_date', $request))
            ->orderByDesc('assigned_date')
            ->get();

        return view('reports.asset-assignment', compact('assignments'));
    }

    // 6. Annual Asset Summary
    public function annualSummary(Request $request)
    {
        $year = $request->integer('year', now()->year);

        $newAssets = Asset::whereYear('created_at', $year)->count();
        $disposedAssets = Asset::whereYear('updated_at', $year)->where('status', 'for_disposal')->count();
        $totalSpendOnPurchases = Asset::whereYear('purchase_date', $year)->sum('purchase_cost');
        $totalRepairCost = RepairHistory::whereYear('repair_date', $year)->sum('cost');
        $totalMaintenanceCompleted = MaintenanceSchedule::whereYear('completed_at', $year)
            ->where('status', 'completed')->count();

        $byCategory = Asset::selectRaw('category, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('category')
            ->get();

        return view('reports.annual-summary', compact(
            'year', 'newAssets', 'disposedAssets', 'totalSpendOnPurchases',
            'totalRepairCost', 'totalMaintenanceCompleted', 'byCategory'
        ));
    }

    public function monthlySummary(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $newAssets = Asset::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $disposedAssets = Asset::whereMonth('updated_at', $month)->whereYear('updated_at', $year)->where('status', 'for_disposal')->count();
        $totalSpendOnPurchases = Asset::whereMonth('purchase_date', $month)->whereYear('purchase_date', $year)->sum('purchase_cost');
        $totalRepairCost = RepairHistory::whereMonth('repair_date', $month)->whereYear('repair_date', $year)->sum('cost');
        $totalMaintenanceCompleted = MaintenanceSchedule::whereMonth('completed_at', $month)->whereYear('completed_at', $year)
            ->where('status', 'completed')->count();

        $byCategory = Asset::selectRaw('category, COUNT(*) as total')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('category')
            ->get();

        return view('reports.monthly-summary', compact(
            'month', 'year', 'newAssets', 'disposedAssets', 'totalSpendOnPurchases',
            'totalRepairCost', 'totalMaintenanceCompleted', 'byCategory'
        ));
    }

    /**
     * Generic CSV export, reused by any report route with ?export=csv.
     * Usage: pass a Collection and column map.
     */
    public static function exportCsv(string $filename, iterable $rows, array $headers): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($rows, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename);
    }
}
