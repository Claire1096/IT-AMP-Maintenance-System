#!/bin/bash
set -e
mkdir -p database/migrations app/Models app/Http/Controllers resources/views/assets

echo 'Writing database/migrations/2026_02_02_000001_rename_notes_to_description_on_assets_table.php'
cat > database/migrations/2026_02_02_000001_rename_notes_to_description_on_assets_table.php << 'MARK1'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->renameColumn('notes', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->renameColumn('description', 'notes');
        });
    }
};

MARK1

echo 'Writing app/Models/Asset.php'
cat > app/Models/Asset.php << 'MARK2'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_tag', 'name', 'category_id', 'brand', 'model', 'serial_number',
        'assigned_employee_id', 'department_id', 'location_id',
        'purchase_date', 'purchase_cost', 'warranty_expiration', 'supplier_id',
        'status', 'description', 'qr_code_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiration' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    const STATUSES = ['active', 'under_repair', 'for_disposal', 'lost'];

    // --- Relationships ---

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function repairHistories(): HasMany
    {
        return $this->hasMany(RepairHistory::class);
    }

    // --- Helpers ---

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiration && $this->warranty_expiration->isFuture();
    }

    public function nextMaintenanceDate(): ?string
    {
        return $this->maintenanceSchedules()
            ->whereIn('status', ['scheduled', 'overdue'])
            ->orderBy('next_maintenance_date')
            ->value('next_maintenance_date');
    }
}

MARK2

echo 'Writing app/Http/Controllers/AssetController.php'
cat > app/Http/Controllers/AssetController.php << 'MARK3'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetMovement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // composer require simplesoftwareio/simple-qrcode

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::query()
            ->with(['category', 'assignedEmployee', 'department', 'location', 'supplier'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('asset_tag', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%")
                        ->orWhere('serial_number', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Asset::count(),
            'active' => Asset::where('status', 'active')->count(),
            'under_repair' => Asset::where('status', 'under_repair')->count(),
            'expiring_soon' => Asset::whereNotNull('warranty_expiration')
                ->whereBetween('warranty_expiration', [now(), now()->addDays(30)])
                ->count(),
        ];

        return view('assets.index', [
            'assets' => $assets,
            'stats' => $stats,
            'categories' => AssetCategory::all(),
            'departments' => Department::all(),
            'locations' => Location::with('building')->get(),
        ]);
    }

    public function create()
    {
        return view('assets.create', [
            'categories' => AssetCategory::all(),
            'employees' => Employee::where('is_active', true)->orderBy('first_name')->get(),
            'departments' => Department::all(),
            'locations' => Location::with('building')->get(),
            'suppliers' => Supplier::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expiration' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
        ]);

        $validated['asset_tag'] = $this->generateAssetTag($validated['category_id']);
        $validated['status'] = 'active';

        $asset = Asset::create($validated);
        $this->generateQrCode($asset);

        if ($asset->assigned_employee_id) {
            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $asset->assigned_employee_id,
                'department_id' => $asset->department_id,
                'assigned_by' => auth()->id(),
                'assigned_date' => now(),
            ]);
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Asset registered successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'category', 'assignedEmployee', 'department', 'location', 'supplier',
            'assignments.employee', 'movements.fromLocation', 'movements.toLocation',
            'maintenanceSchedules' => fn ($q) => $q->latest('scheduled_date'),
            'repairHistories' => fn ($q) => $q->latest('reported_date'),
        ]);

        $employees = \App\Models\Employee::where('is_active', true)->orderBy('first_name')->get();
        $departments = \App\Models\Department::all();

        return view('assets.show', compact('asset', 'employees', 'departments'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => AssetCategory::all(),
            'employees' => Employee::where('is_active', true)->orderBy('first_name')->get(),
            'departments' => Department::all(),
            'locations' => Location::with('building')->get(),
            'suppliers' => Supplier::all(),
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number,' . $asset->id,
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expiration' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|in:active,under_repair,for_disposal,lost',
            'description' => 'nullable|string',
        ]);

        // Log a movement if location changed
        if ($asset->location_id != ($validated['location_id'] ?? null)) {
            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_location_id' => $asset->location_id,
                'to_location_id' => $validated['location_id'] ?? null,
                'moved_by' => auth()->id(),
                'moved_at' => now(),
            ]);
        }

        $asset->update($validated);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated.');
    }

    /**
     * Reassign an asset to a different employee (creates assignment history record).
     */
    public function reassign(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'remarks' => 'nullable|string',
        ]);

        // Close out previous assignment
        $asset->assignments()->whereNull('returned_date')->update(['returned_date' => now()]);

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'] ?? null,
            'assigned_by' => auth()->id(),
            'assigned_date' => now(),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $asset->update([
            'assigned_employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'] ?? $asset->department_id,
        ]);

        return back()->with('success', 'Asset reassigned successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete(); // soft delete

        return redirect()->route('assets.index')->with('success', 'Asset removed.');
    }

    // --- Helpers ---

    private function generateAssetTag(int $categoryId): string
    {
        $category = AssetCategory::findOrFail($categoryId);
        $prefix = $category->prefix ?: strtoupper(substr($category->name, 0, 3));
        $year = now()->year;

        $count = Asset::where('category_id', $categoryId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $count);
    }

    private function generateQrCode(Asset $asset): void
    {
        $path = "qrcodes/{$asset->asset_tag}.svg";
        $qr = QrCode::size(300)->generate(route('assets.show', $asset));
        \Storage::disk('public')->put($path, $qr);
        $asset->update(['qr_code_path' => $path]);
    }
}

MARK3

echo 'Writing resources/views/assets/create.blade.php'
cat > resources/views/assets/create.blade.php << 'MARK4'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Asset — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm" x-data="{ showAddEmployee: false }">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">ADD ASSET</h1>
                    <p class="text-xs text-gray-400">Asset Management / Add new asset</p>
                </div>
                <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-3xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('assets.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-6 max-w-3xl">

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET ID</label>
                                <input type="text" disabled placeholder="auto-generated after saving"
                                       class="w-full text-xs border-gray-200 rounded-md bg-gray-50 text-gray-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY *</label>
                                <select name="category_id" required class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="eg. Dell Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="eg. Dell..."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                                <input type="text" name="model" value="{{ old('model') }}" placeholder="eg. Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="eg. 42H5-Y642-W524785.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">QR CODE</label>
                            <div class="text-xs text-gray-400 border border-dashed border-gray-300 rounded-md px-3 py-2">
                                Generated automatically after saving
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                            <textarea name="description" rows="2" class="w-full text-xs border-gray-300 rounded-md">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Asset Assignment</h2>

                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
                                <select name="assigned_employee_id" class="w-full text-xs border-gray-300 rounded-md mb-1">
                                    <option value="">— Unassigned —</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-quick-add-employee-trigger />
                            </div>

                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                                <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                            {{ $location->building->name ?? '' }} — {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#128722; Purchase Details</h3>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                                    <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                                        <option value="">Select</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                                    <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                SAVE CHANGES
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-quick-add-employee-modal :departments="$departments" />
</body>
</html>

MARK4

echo 'Writing resources/views/assets/edit.blade.php'
cat > resources/views/assets/edit.blade.php << 'MARK5'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Asset — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">EDIT ASSET</h1>
                    <p class="text-xs text-gray-400">{{ $asset->asset_tag }} — {{ $asset->name }}</p>
                </div>
                <a href="{{ route('assets.show', $asset) }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-3xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('assets.update', $asset) }}" class="max-w-3xl">
                @csrf
                @method('PUT')

                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS *</label>
                            <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
                                @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $asset->status) === $status)>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                            <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                            <input type="text" name="model" value="{{ old('model', $asset->model) }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                               class="w-full text-xs border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                        <textarea name="description" rows="2" class="w-full text-xs border-gray-300 rounded-md">{{ old('description', $asset->description) }}</textarea>
                    </div>
                </div>

                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Assignment</h2>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— None —</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $asset->department_id) == $department->id)>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                            <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— None —</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id) == $location->id)>
                                        {{ $location->building->name ?? '' }} — {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm mb-6">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128722; Procurement</h2>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                            <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                            <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration', optional($asset->warranty_expiration)->format('Y-m-d')) }}"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                            <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— None —</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $asset->supplier_id) == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                        SAVE CHANGES
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Remove this asset? This can be restored by an admin later.')" class="max-w-3xl mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 text-xs hover:underline">Remove this asset</button>
            </form>
        </div>
    </div>
</body>
</html>

MARK5

echo 'Writing resources/views/assets/show.blade.php'
cat > resources/views/assets/show.blade.php << 'MARK6'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->asset_tag }} — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm" x-data="{ showAddEmployee: false }">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">{{ $asset->asset_tag }} — {{ $asset->name }}</h1>
                    <p class="text-xs text-gray-400">Asset Management / Asset details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-1.5 bg-gray-200 text-gray-800 text-xs font-semibold rounded-full">EDIT</a>
                    <a href="{{ route('maintenance.create', $asset) }}" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-full">SCHEDULE MAINTENANCE</a>
                    <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs max-w-4xl">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-2 gap-6 max-w-4xl mb-6">

                {{-- Asset Details card --}}
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Details</h2>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->category->name ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->brand }} {{ $asset->model }}</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                            <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->serial_number ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
                        <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->assignedEmployee->full_name ?? 'unassigned' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <div class="text-xs bg-rose-50 border border-rose-100 rounded-md px-3 py-2">{{ $asset->department->name ?? 'unassigned' }}</div>
                    </div>

                    <form method="POST" action="{{ route('assets.reassign', $asset) }}">
                        @csrf
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">REASSIGN TO</label>
                        <select name="employee_id" required class="w-full text-xs border-gray-300 rounded-md mb-1">
                            <option value="">unassigned</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                        <div class="mb-3">
                            <x-quick-add-employee-trigger />
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-5 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                REASSIGN
                            </button>
                        </div>
                    </form>
                </div>

                {{-- QR Code card --}}
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col items-center justify-center text-center">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Asset QR Code</h2>
                    @if ($asset->qr_code_path)
                        <img src="{{ Storage::url($asset->qr_code_path) }}" alt="QR code" class="w-48 h-48 mb-3">
                    @else
                        <div class="w-48 h-48 mb-3 flex items-center justify-center text-gray-300 border border-dashed border-gray-300 rounded-md">
                            No QR generated
                        </div>
                    @endif
                    <p class="text-xs font-semibold text-gray-700">ASSET ID: {{ $asset->asset_tag }}</p>
                </div>
            </div>

            {{-- Procurement info --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Procurement</h2>
                <div class="grid grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                        {{ $asset->supplier->name ?? '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                        {{ optional($asset->purchase_date)->format('M d, Y') ?? '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                        {{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '—' }}
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY</label>
                        <span class="{{ $asset->isUnderWarranty() ? 'text-green-700' : 'text-red-600' }}">
                            {{ optional($asset->warranty_expiration)->format('M d, Y') ?? '—' }}
                        </span>
                    </div>
                </div>
                @if ($asset->description)
                    <div class="mt-3">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DESCRIPTION</label>
                        <p class="text-xs">{{ $asset->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Maintenance schedules --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Preventive Maintenance</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-[10px] text-gray-500 uppercase">
                            <th class="py-2">Type</th>
                            <th>Frequency</th>
                            <th>Next Date</th>
                            <th>Status</th>
                            <th>Technician</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($asset->maintenanceSchedules as $schedule)
                            <tr>
                                <td class="py-2">{{ $schedule->maintenance_type }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                <td>{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                                <td>{{ ucwords($schedule->status) }}</td>
                                <td>{{ $schedule->technician->name ?? '—' }}</td>
                                <td>
                                    @if (in_array($schedule->status, ['scheduled', 'overdue']))
                                        <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                                            @csrf
                                            <button class="text-pink-600 hover:underline">Mark Complete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No maintenance scheduled yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Repair history --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm max-w-4xl mb-6">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Repair History</h2>
                <table class="min-w-full text-xs divide-y divide-rose-100">
                    <thead>
                        <tr class="text-left text-[10px] text-gray-500 uppercase">
                            <th class="py-2">Reported</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Cost</th>
                            <th>Downtime (hrs)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($asset->repairHistories as $repair)
                            <tr>
                                <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                                <td>{{ Str::limit($repair->issue_description, 60) }}</td>
                                <td>{{ ucwords($repair->status) }}</td>
                                <td>{{ number_format($repair->cost, 2) }}</td>
                                <td>{{ $repair->downtime_hours ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No repairs logged yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Assignment & movement history --}}
            <div class="grid grid-cols-2 gap-6 max-w-4xl">
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Assignment History</h2>
                    <ul class="text-xs space-y-2">
                        @forelse ($asset->assignments as $assignment)
                            <li class="border-b border-rose-50 pb-2">
                                {{ $assignment->employee->full_name ?? '—' }}
                                <span class="text-gray-400 text-[10px] block">
                                    {{ $assignment->assigned_date->format('M d, Y') }}
                                    @if ($assignment->returned_date) – {{ $assignment->returned_date->format('M d, Y') }} @endif
                                </span>
                            </li>
                        @empty
                            <li class="text-gray-400">No assignment history.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-3">Movement History</h2>
                    <ul class="text-xs space-y-2">
                        @forelse ($asset->movements as $movement)
                            <li class="border-b border-rose-50 pb-2">
                                {{ $movement->fromLocation->name ?? 'Unknown' }} → {{ $movement->toLocation->name ?? 'Unknown' }}
                                <span class="text-gray-400 text-[10px] block">{{ $movement->moved_at->format('M d, Y g:i A') }}</span>
                            </li>
                        @empty
                            <li class="text-gray-400">No movement history.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-quick-add-employee-modal :departments="$departments" />
</body>
</html>

MARK6

echo 'Renamed notes to description everywhere! Run php artisan migrate next.'