<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\DamageReport;
use App\Models\FacilityItem;
use Illuminate\Http\Request;

class DamageReportController extends Controller
{
    private array $categories = ['Facility and Maintenance', 'Fixed Asset Inventory'];
    private array $causes = ['accident', 'negligence', 'wear_and_tear', 'natural_causes', 'other'];
    private array $actions = ['repaired', 'replaced', 'sent_for_maintenance', 'disposed'];

    public function index()
    {
        $reports = DamageReport::latest()->paginate(20);

        return view('reports.damage-index', compact('reports'));
    }

    public function create()
    {
        return view('reports.damage-create', [
            'categories' => $this->categories,
            'causes' => $this->causes,
            'actions' => $this->actions,
            'facilityItems' => FacilityItem::orderBy('name')->get(['id', 'name', 'item_tag', 'asset_type']),
            'assets' => Asset::with('category')->orderBy('name')->get(['id', 'name', 'asset_tag', 'category_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', $this->categories),
            'facility_item_id' => 'nullable|exists:facility_items,id',
            'asset_id' => 'nullable|exists:assets,id',
            'asset_name' => 'nullable|string|max:255',
            'asset_type' => 'nullable|string|max:255',
            'asset_tag_no' => 'nullable|string|max:255',
            'date_reported' => 'required|date',
            'date_of_incident' => 'nullable|date',
            'time_of_incident' => 'nullable|date_format:H:i',
            'type_of_incident' => 'nullable|string|max:255',
            'cause_of_damage' => 'nullable|in:' . implode(',', $this->causes),
            'cause_other_note' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'action_taken' => 'nullable|in:' . implode(',', $this->actions),
            'inspected_by' => 'nullable|string|max:255',
            'inspection_date' => 'nullable|date',
            'condition' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'facilitator_name' => 'nullable|string|max:255',
            'facilitator_date' => 'nullable|date',
        ]);

        $validated['report_number'] = 'DR-' . now()->year . '-' . str_pad(DamageReport::whereYear('created_at', now()->year)->count() + 1, 4, '0', STR_PAD_LEFT);

        $report = DamageReport::create($validated);

        return redirect()->route('reports.damage.show', $report)->with('success', 'Damage report saved.');
    }

    public function show(DamageReport $damageReport)
    {
        return view('reports.damage-show', ['report' => $damageReport]);
    }
}