<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FinanceItem;
use Illuminate\Http\Request;

class FinanceItemController extends Controller
{
    private array $assetTypes = ['tools', 'supplies', 'equipment', 'electronics', 'furniture', 'vehicles', 'machinery'];
    private array $statuses = ['in_use', 'in_storage', 'damaged', 'disposed', 'missing'];

    public function index(Request $request)
    {
        $items = FinanceItem::query()
            ->with('department')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('item_tag', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%");
                });
            })
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->latest()
            ->simplePaginate(12)
            ->withQueryString();

        $stats = [
            'total' => FinanceItem::count(),
            'active' => FinanceItem::where('status', 'in_use')->count(),
            'missing' => FinanceItem::where('status', 'missing')->count(),
        ];

        return view('finance-items.index', [
            'items' => $items,
            'departments' => Department::all(),
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return view('finance-items.create', [
            'departments' => Department::all(),
            'assetTypes' => $this->assetTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_type' => 'nullable|in:' . implode(',', $this->assetTypes),
            'quantity' => 'required|integer|min:0',
            'current_quantity' => 'required|integer|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:' . implode(',', $this->statuses),
        ]);

        $validated['missing_quantity'] = max(0, $validated['quantity'] - $validated['current_quantity']);
        $validated['item_tag'] = $this->generateItemTag();

        if ($validated['status'] === 'missing') {
            $validated['missing_since'] = now();
        }

        $item = FinanceItem::create($validated);

        $item->monthlyLogs()->create([
            'month' => now()->startOfMonth(),
            'quantity_on_hand' => $validated['current_quantity'],
            'missing_quantity' => $validated['missing_quantity'],
        ]);

        return redirect()->route('finance-items.show', $item)->with('success', 'Item registered successfully.');
    }

    public function show(FinanceItem $financeItem)
    {
        $financeItem->load(['department', 'monthlyLogs' => fn ($q) => $q->orderBy('month')]);

        return view('finance-items.show', ['item' => $financeItem]);
    }

    public function edit(FinanceItem $financeItem)
    {
        return view('finance-items.edit', [
            'item' => $financeItem,
            'departments' => Department::all(),
            'assetTypes' => $this->assetTypes,
        ]);
    }

    public function update(Request $request, FinanceItem $financeItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_type' => 'nullable|in:' . implode(',', $this->assetTypes),
            'quantity' => 'required|integer|min:0',
            'current_quantity' => 'required|integer|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:' . implode(',', $this->statuses),
        ]);

        $validated['missing_quantity'] = max(0, $validated['quantity'] - $validated['current_quantity']);

        if ($validated['status'] === 'missing' && $financeItem->status !== 'missing') {
            $validated['missing_since'] = now();
        } elseif ($validated['status'] !== 'missing') {
            $validated['missing_since'] = null;
        }

        $financeItem->update($validated);

        $financeItem->monthlyLogs()->updateOrCreate(
            ['month' => now()->startOfMonth()],
            ['quantity_on_hand' => $validated['current_quantity'], 'missing_quantity' => $validated['missing_quantity']]
        );

        return redirect()->route('finance-items.show', $financeItem)->with('success', 'Item updated.');
    }

    public function destroy(FinanceItem $financeItem)
    {
        $financeItem->delete();

        return redirect()->route('finance-items.index')->with('success', 'Item removed.');
    }

    private function generateItemTag(): string
    {
        $year = now()->year;
        $count = FinanceItem::withTrashed()->where('item_tag', 'like', "FIN-{$year}-%")->count() + 1;

        return sprintf('FIN-%d-%04d', $year, $count);
    }
}
