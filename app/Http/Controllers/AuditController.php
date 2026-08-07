<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FinanceItem;
use Illuminate\Http\Request;

class AuditController extends Controller
{
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

        if ($request->ajax()) {
            return view('audit._results', compact('items', 'stats'))->render();
        }

        return view('audit.index', [
            'items' => $items,
            'departments' => Department::all(),
            'stats' => $stats,
        ]);
    }
}
