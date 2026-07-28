<?php
namespace App\Http\Controllers;
use App\Models\Asset;
use App\Models\DamageReport;
use App\Models\Employee;
use App\Models\FacilityItem;
use Illuminate\Http\Request;
class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $assets = collect();
        $employees = collect();
        $facilityItems = collect();

        if (trim($query) !== '') {
            $assets = Asset::query()
                ->with(['category', 'department', 'assignedEmployee'])
                ->where(function ($q) use ($query) {
                    $q->where('asset_tag', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhere('serial_number', 'like', "%{$query}%")
                        ->orWhere('brand', 'like', "%{$query}%")
                        ->orWhere('model', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            $employees = Employee::query()
                ->with('department')
                ->where(function ($q) use ($query) {
                    $q->where('employee_id', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            $facilityItems = FacilityItem::query()
    ->with('department')
    ->where(function ($q) use ($query) {
        $q->where('item_tag', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('asset_type', 'like', "%{$query}%");
    })
    ->limit(10)
    ->get();

$damageReports = DamageReport::query()
    ->where(function ($q) use ($query) {
        $q->where('report_number', 'like', "%{$query}%")
            ->orWhere('asset_name', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('cause_of_damage', 'like', "%{$query}%");
    })
    ->limit(10)
    ->get();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'query' => $query,
                'assets' => $assets->map(fn ($a) => [
                    'name' => $a->name,
                    'asset_tag' => $a->asset_tag,
                    'category' => $a->category->name ?? null,
                    'assigned_to' => $a->assignedEmployee->full_name ?? null,
                    'url' => route('assets.show', $a),
                ]),
                'employees' => $employees->map(fn ($e) => [
                    'name' => $e->full_name,
                    'department' => $e->department->name ?? null,
                    'url' => route('employees.show', $e),
                ]),
                'facilityItems' => $facilityItems->map(fn ($f) => [
                    'name' => $f->name,
                    'item_tag' => $f->item_tag,
                    'category' => $f->category,
                    'department' => $f->department->name ?? null,
                    'url' => route('facility-items.show', $f),
                ]),
                'damageReports' => $damageReports->map(fn ($r) => [
                    'report_number' => $r->report_number,
                    'asset_name' => $r->asset_name,
                    'cause' => $r->cause_of_damage,
                    'url' => route('reports.damage.show', $r),
                ]),
            ]);
        }

        return view('search.index', [
            'query' => $query,
            'assets' => $assets,
            'employees' => $employees,
            'facilityItems' => $facilityItems,
        ]);
    }
}