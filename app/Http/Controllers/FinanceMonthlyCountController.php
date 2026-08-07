<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FinanceItem;
use App\Models\FinanceMonthlyCount;
use App\Models\FinanceMonthlyCountItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceMonthlyCountController extends Controller
{
    public function index()
    {
        $counts = FinanceMonthlyCount::withCount('items')
            ->with('creator')
            ->orderByDesc('month')
            ->paginate(12);

        return view('finance-counts.index', compact('counts'));
    }

    public function create()
    {
        $openExists = FinanceMonthlyCount::where('status', 'open')->exists();

        if ($openExists) {
            return redirect()->route('finance-counts.index')
                ->with('error', 'There is already an open count. Close it before starting a new one.');
        }

        $count = DB::transaction(function () {
            $count = FinanceMonthlyCount::create([
                'month' => now()->startOfMonth(),
                'status' => 'open',
                'created_by' => auth()->id(),
            ]);

            FinanceItem::all()->each(function ($item) use ($count) {
                FinanceMonthlyCountItem::create([
                    'finance_monthly_count_id' => $count->id,
                    'finance_item_id' => $item->id,
                    'expected_quantity' => $item->quantity,
                    'department_id' => $item->department_id,
                ]);
            });

            return $count;
        });

        return redirect()->route('finance-counts.show', $count)->with('success', 'Monthly count started.');
    }

    public function show(Request $request, FinanceMonthlyCount $financeCount)
    {
        $items = $financeCount->items()
            ->with(['financeItem', 'department'])
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('financeItem', function ($sub) use ($request) {
                    $sub->where('item_tag', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%");
                });
            })
            ->when($request->status === 'unchecked', fn ($q) => $q->whereNull('checked_at'))
            ->when($request->status === 'checked', fn ($q) => $q->whereNotNull('checked_at'))
            ->latest('id')
            ->simplePaginate(20)
            ->withQueryString();

        $progress = [
            'total' => $financeCount->items()->count(),
            'checked' => $financeCount->items()->whereNotNull('checked_at')->count(),
        ];

        if ($request->ajax()) {
            return view('finance-counts._checklist', compact('financeCount', 'items', 'progress'))->render();
        }

        return view('finance-counts.show', [
            'financeCount' => $financeCount,
            'items' => $items,
            'progress' => $progress,
            'departments' => Department::all(),
        ]);
    }

    public function updateItem(Request $request, FinanceMonthlyCount $financeCount, FinanceMonthlyCountItem $item)
    {
        abort_if($financeCount->status !== 'open', 403, 'This count is closed.');

        $validated = $request->validate([
            'counted_quantity' => 'required|integer|min:0',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $item->update([
            'counted_quantity' => $validated['counted_quantity'],
            'department_id' => $validated['department_id'] ?? $item->department_id,
            'checked_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function reopen(FinanceMonthlyCount $financeCount)
    {
        abort_if($financeCount->status !== 'closed', 403, 'This count is not closed.');

        $otherOpenExists = FinanceMonthlyCount::where('status', 'open')->where('id', '!=', $financeCount->id)->exists();

        if ($otherOpenExists) {
            return redirect()->route('finance-counts.index')
                ->with('error', 'Another count is already open. Close it before reopening this one.');
        }

        $financeCount->update(['status' => 'open', 'closed_at' => null]);

        return redirect()->route('finance-counts.show', $financeCount)->with('success', 'Count reopened.');
    }

    public function destroy(FinanceMonthlyCount $financeCount)
    {
        $financeCount->delete();

        return redirect()->route('finance-counts.index')->with('success', 'Monthly count deleted.');
    }

    public function close(FinanceMonthlyCount $financeCount)
    {
        abort_if($financeCount->status !== 'open', 403, 'This count is already closed.');

        DB::transaction(function () use ($financeCount) {
            $financeCount->items()->whereNull('checked_at')->update([
                'counted_quantity' => 0,
                'checked_at' => now(),
            ]);

            $financeCount->items()->with('financeItem')->get()->each(function ($countItem) use ($financeCount) {
                $item = $countItem->financeItem;
                if (!$item) {
                    return;
                }

                $missing = max(0, $countItem->expected_quantity - $countItem->counted_quantity);

                $item->update([
                    'current_quantity' => $countItem->counted_quantity,
                    'missing_quantity' => $missing,
                    'department_id' => $countItem->department_id,
                    'status' => $missing > 0 ? 'missing' : 'in_use',
                    'missing_since' => $missing > 0 ? ($item->missing_since ?? now()) : null,
                ]);

                $item->monthlyLogs()->updateOrCreate(
                    ['month' => $financeCount->month],
                    ['quantity_on_hand' => $countItem->counted_quantity, 'missing_quantity' => $missing]
                );
            });

            $financeCount->update(['status' => 'closed', 'closed_at' => now()]);
        });

        return redirect()->route('finance-counts.show', $financeCount)->with('success', 'Count closed and finance items updated.');
    }
}
