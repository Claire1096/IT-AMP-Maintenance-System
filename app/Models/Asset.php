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
        'status', 'notes', 'qr_code_path',
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

