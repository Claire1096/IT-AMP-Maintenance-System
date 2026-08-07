<?php

namespace App\Http\Controllers;

use App\Models\FinanceItem;
use App\Models\FinanceItemMonthlyLog;
use Illuminate\Support\Facades\DB;

class AuditReportController extends Controller
{
    public function index()
    {
        $months = FinanceItemMonthlyLog::select(DB::raw("DATE_FORMAT(month, '%Y-%m') as period"))
            ->distinct()
            ->orderBy('period')
            ->pluck('period');

        $missingByMonth = FinanceItemMonthlyLog::where('missing_quantity', '>', 0)
            ->select(
                DB::raw("DATE_FORMAT(month, '%Y-%m') as period"),
                DB::raw('COUNT(*) as missing_count')
            )
            ->groupBy('period')
            ->pluck('missing_count', 'period');

        $baseline = FinanceItem::count();
        $runningTotal = $baseline;
        $summary = collect();

        foreach ($months as $index => $period) {
            $missingThisMonth = $missingByMonth[$period] ?? 0;

            $summary->push([
                'label' => \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y'),
                'original_quantity' => $runningTotal,
                'missing' => $missingThisMonth,
            ]);

            $runningTotal = max(0, $runningTotal - $missingThisMonth);
        }

        return view('audit-reports.index', compact('summary'));
    }
}
