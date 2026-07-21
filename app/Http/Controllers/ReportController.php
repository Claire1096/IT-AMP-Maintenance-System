<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\MaintenanceSchedule;
use App\Models\RepairHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // 1. Asset Inventory Report
    public function inventory(Request $request)
    {
        $assets = Asset::with(['category', 'department', 'location', 'assignedEmployee'])
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
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
            ->orderBy('scheduled_date')
            ->get();

        return view('reports.preventive-maintenance', compact('schedules'));
    }

    // 3. Warranty Expiration Report
    public function warrantyExpiration(Request $request)
    {
        $withinDays = $request->integer('within_days', 90);

        $assets = Asset::with(['category', 'department'])
            ->whereNotNull('warranty_expiration')
            ->whereDate('warranty_expiration', '<=', now()->addDays($withinDays))
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

        $byCategory = Asset::selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,name')
            ->whereYear('created_at', $year)
            ->groupBy('category_id')
            ->get();

        return view('reports.annual-summary', compact(
            'year', 'newAssets', 'disposedAssets', 'totalSpendOnPurchases',
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

