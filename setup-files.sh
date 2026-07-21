#!/bin/bash
set -e
echo "Creating directories..."
mkdir -p database/migrations app/Models app/Http/Controllers app/Console/Commands app/Notifications

echo 'Writing 2026_01_01_000001_create_departments_table.php'
cat > database/migrations/2026_01_01_000001_create_departments_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000002_create_buildings_table.php'
cat > database/migrations/2026_01_01_000002_create_buildings_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000003_create_locations_table.php'
cat > database/migrations/2026_01_01_000003_create_locations_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "3rd Floor - IT Room"
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000004_create_suppliers_table.php'
cat > database/migrations/2026_01_01_000004_create_suppliers_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000005_create_asset_categories_table.php'
cat > database/migrations/2026_01_01_000005_create_asset_categories_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Desktop, Laptop, Mobile Phone, Printer, Router, Switch, Monitor, UPS
            $table->string('prefix', 5)->nullable(); // used for asset tag generation e.g. DSK, LAP, MOB
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000006_create_employees_table.php'
cat > database/migrations/2026_01_01_000006_create_employees_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique()->nullable(); // company employee number
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000007_add_role_to_users_table.php'
cat > database/migrations/2026_01_01_000007_add_role_to_users_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin: full access | technician: manages maintenance/repairs | viewer: department head, read-only reports
            $table->string('role')->default('viewer')->after('email');
            $table->foreignId('department_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn('role');
        });
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000008_create_assets_table.php'
cat > database/migrations/2026_01_01_000008_create_assets_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); // e.g. LAP-2026-0001, encoded in QR
            $table->string('name'); // e.g. "Dell Latitude 5420"
            $table->foreignId('category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();

            // Assignment
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();

            // Procurement
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->date('warranty_expiration')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            // Status
            $table->enum('status', ['active', 'under_repair', 'for_disposal', 'lost'])->default('active');

            $table->text('notes')->nullable();
            $table->string('qr_code_path')->nullable(); // stored generated QR image path

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000009_create_asset_assignments_table.php'
cat > database/migrations/2026_01_01_000009_create_asset_assignments_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000010_create_asset_movements_table.php'
cat > database/migrations/2026_01_01_000010_create_asset_movements_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('moved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('moved_at');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000011_create_maintenance_schedules_table.php'
cat > database/migrations/2026_01_01_000011_create_maintenance_schedules_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('maintenance_type'); // e.g. "Cleaning", "OS Update", "Hardware Check"
            $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('quarterly');
            $table->date('scheduled_date');
            $table->date('next_maintenance_date')->nullable();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'overdue', 'skipped'])->default('scheduled');
            $table->text('technician_remarks')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000012_create_maintenance_checklist_items_table.php'
cat > database/migrations/2026_01_01_000012_create_maintenance_checklist_items_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('task_description'); // e.g. "Check fan/thermal paste", "Update antivirus"
            $table->boolean('is_completed')->default(false);
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_checklist_items');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000013_create_repair_histories_table.php'
cat > database/migrations/2026_01_01_000013_create_repair_histories_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained()->nullOnDelete(); // link if repair came from a PM check
            $table->date('reported_date');
            $table->text('issue_description');
            $table->date('repair_date')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('cost', 12, 2)->default(0);
            $table->decimal('downtime_hours', 8, 2)->nullable();
            $table->enum('status', ['reported', 'in_progress', 'completed', 'unrepairable'])->default('reported');
            $table->text('technician_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_histories');
    }
};

EOF_MIGRATION

echo 'Writing 2026_01_01_000014_create_repair_parts_table.php'
cat > database/migrations/2026_01_01_000014_create_repair_parts_table.php << 'EOF_MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_history_id')->constrained()->cascadeOnDelete();
            $table->string('part_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_parts');
    }
};

EOF_MIGRATION

echo 'Writing Models/Asset.php'
cat > app/Models/Asset.php << 'EOF_MODEL'
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

EOF_MODEL

echo 'Writing Models/AssetAssignment.php'
cat > app/Models/AssetAssignment.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'employee_id', 'department_id', 'assigned_by',
        'assigned_date', 'returned_date', 'remarks',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'returned_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

EOF_MODEL

echo 'Writing Models/AssetCategory.php'
cat > app/Models/AssetCategory.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'prefix'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}

EOF_MODEL

echo 'Writing Models/AssetMovement.php'
cat > app/Models/AssetMovement.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'from_location_id', 'to_location_id', 'moved_by', 'moved_at', 'remarks',
    ];

    protected $casts = [
        'moved_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}

EOF_MODEL

echo 'Writing Models/Building.php'
cat > app/Models/Building.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address'];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}

EOF_MODEL

echo 'Writing Models/Department.php'
cat > app/Models/Department.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

EOF_MODEL

echo 'Writing Models/Employee.php'
cat > app/Models/Employee.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'first_name', 'last_name', 'email', 'position', 'department_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_employee_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

EOF_MODEL

echo 'Writing Models/Location.php'
cat > app/Models/Location.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'name', 'floor', 'room'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}

EOF_MODEL

echo 'Writing Models/MaintenanceChecklistItem.php'
cat > app/Models/MaintenanceChecklistItem.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id', 'task_description', 'is_completed', 'notes', 'sort_order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}

EOF_MODEL

echo 'Writing Models/MaintenanceSchedule.php'
cat > app/Models/MaintenanceSchedule.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'maintenance_type', 'frequency', 'scheduled_date',
        'next_maintenance_date', 'assigned_technician_id', 'status',
        'technician_remarks', 'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'next_maintenance_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(MaintenanceChecklistItem::class);
    }

    public function repairHistories(): HasMany
    {
        return $this->hasMany(RepairHistory::class);
    }

    /**
     * Compute the next maintenance date based on frequency, from a given base date.
     */
    public function calculateNextDate(\DateTimeInterface $from): ?\Carbon\Carbon
    {
        $date = \Carbon\Carbon::parse($from);

        return match ($this->frequency) {
            'monthly' => $date->copy()->addMonth(),
            'quarterly' => $date->copy()->addMonths(3),
            'semi_annual' => $date->copy()->addMonths(6),
            'annual' => $date->copy()->addYear(),
            default => null, // one_time has no next date
        };
    }
}

EOF_MODEL

echo 'Writing Models/RepairHistory.php'
cat > app/Models/RepairHistory.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'maintenance_schedule_id', 'reported_date', 'issue_description',
        'repair_date', 'technician_id', 'cost', 'downtime_hours', 'status', 'technician_remarks',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'repair_date' => 'date',
        'cost' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function getTotalPartsCostAttribute(): float
    {
        return (float) $this->parts()->sum(\DB::raw('quantity * unit_cost'));
    }
}

EOF_MODEL

echo 'Writing Models/RepairPart.php'
cat > app/Models/RepairPart.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairPart extends Model
{
    use HasFactory;

    protected $fillable = ['repair_history_id', 'part_name', 'quantity', 'unit_cost'];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function repairHistory(): BelongsTo
    {
        return $this->belongsTo(RepairHistory::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_cost;
    }
}

EOF_MODEL

echo 'Writing Models/Supplier.php'
cat > app/Models/Supplier.php << 'EOF_MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_person', 'phone', 'email', 'address'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}

EOF_MODEL

echo 'Writing Models/User.php'
cat > app/Models/User.php << 'EOF_MODEL'
<?php

namespace App\Models;

// Illuminate\Foundation\Auth\User as Authenticatable
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_TECHNICIAN = 'technician';
    const ROLE_VIEWER = 'viewer'; // e.g. department head, read-only

    protected $fillable = [
        'name', 'email', 'password', 'role', 'department_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTechnician(): bool
    {
        return $this->role === self::ROLE_TECHNICIAN;
    }
}

EOF_MODEL

echo 'Writing Controllers/AssetController.php'
cat > app/Http/Controllers/AssetController.php << 'EOF_CTRL'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetMovement;
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
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('asset_tag', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%")
                        ->orWhere('serial_number', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create', [
            'categories' => AssetCategory::all(),
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
            'notes' => 'nullable|string',
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

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => AssetCategory::all(),
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
            'notes' => 'nullable|string',
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

EOF_CTRL

echo 'Writing Controllers/DashboardController.php'
cat > app/Http/Controllers/DashboardController.php << 'EOF_CTRL'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Department;
use App\Models\MaintenanceSchedule;
use App\Models\RepairHistory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $underRepair = Asset::where('status', 'under_repair')->count();
        $forDisposal = Asset::where('status', 'for_disposal')->count();
        $lost = Asset::where('status', 'lost')->count();

        $warrantyExpiringSoon = Asset::whereNotNull('warranty_expiration')
            ->whereBetween('warranty_expiration', [now(), now()->addDays(30)])
            ->count();
        $warrantyExpired = Asset::whereNotNull('warranty_expiration')
            ->where('warranty_expiration', '<', now())
            ->count();

        $maintenanceDueThisMonth = MaintenanceSchedule::whereIn('status', ['scheduled', 'overdue'])
            ->whereMonth('next_maintenance_date', now()->month)
            ->whereYear('next_maintenance_date', now()->year)
            ->count();

        $assetsByDepartment = Department::withCount('assets')->get(['id', 'name']);

        // Monthly maintenance report: completed schedules + repair costs, grouped by month, last 6 months
        $monthlyMaintenance = MaintenanceSchedule::selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as month, COUNT(*) as completed_count")
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRepairCost = RepairHistory::selectRaw("DATE_FORMAT(repair_date, '%Y-%m') as month, SUM(cost) as total_cost")
            ->whereNotNull('repair_date')
            ->where('repair_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $assetsByCategory = Asset::selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get();

        return view('dashboard.index', compact(
            'totalAssets', 'activeAssets', 'underRepair', 'forDisposal', 'lost',
            'warrantyExpiringSoon', 'warrantyExpired', 'maintenanceDueThisMonth',
            'assetsByDepartment', 'monthlyMaintenance', 'monthlyRepairCost', 'assetsByCategory'
        ));
    }
}

EOF_CTRL

echo 'Writing Controllers/MaintenanceScheduleController.php'
cat > app/Http/Controllers/MaintenanceScheduleController.php << 'EOF_CTRL'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceChecklistItem;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = MaintenanceSchedule::query()
            ->with(['asset', 'technician'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->due_this_month, function ($q) {
                $q->whereMonth('next_maintenance_date', now()->month)
                  ->whereYear('next_maintenance_date', now()->year);
            })
            ->orderBy('next_maintenance_date')
            ->paginate(20);

        return view('maintenance.index', compact('schedules'));
    }

    public function create(Asset $asset)
    {
        return view('maintenance.create', compact('asset'));
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string|max:255',
            'frequency' => 'required|in:one_time,monthly,quarterly,semi_annual,annual',
            'scheduled_date' => 'required|date',
            'assigned_technician_id' => 'nullable|exists:users,id',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string|max:255',
        ]);

        $schedule = MaintenanceSchedule::create([
            'asset_id' => $asset->id,
            'maintenance_type' => $validated['maintenance_type'],
            'frequency' => $validated['frequency'],
            'scheduled_date' => $validated['scheduled_date'],
            'next_maintenance_date' => $validated['scheduled_date'],
            'assigned_technician_id' => $validated['assigned_technician_id'] ?? null,
            'status' => 'scheduled',
        ]);

        foreach ($validated['checklist'] ?? [] as $i => $task) {
            MaintenanceChecklistItem::create([
                'maintenance_schedule_id' => $schedule->id,
                'task_description' => $task,
                'sort_order' => $i,
            ]);
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Maintenance scheduled.');
    }

    /**
     * Mark a scheduled maintenance as completed, roll forward the next date if recurring.
     */
    public function complete(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'technician_remarks' => 'nullable|string',
            'checklist' => 'nullable|array', // [item_id => bool]
        ]);

        foreach ($validated['checklist'] ?? [] as $itemId => $checked) {
            MaintenanceChecklistItem::where('id', $itemId)
                ->where('maintenance_schedule_id', $schedule->id)
                ->update(['is_completed' => (bool) $checked]);
        }

        $nextDate = $schedule->calculateNextDate(now());

        $schedule->update([
            'status' => 'completed',
            'technician_remarks' => $validated['technician_remarks'] ?? $schedule->technician_remarks,
            'completed_at' => now(),
        ]);

        // If recurring, create the next occurrence automatically
        if ($nextDate) {
            $next = $schedule->replicate(['status', 'completed_at', 'technician_remarks']);
            $next->scheduled_date = $nextDate;
            $next->next_maintenance_date = $nextDate;
            $next->status = 'scheduled';
            $next->save();
        }

        return back()->with('success', 'Maintenance marked as completed.');
    }

    /**
     * Flip any schedule whose next_maintenance_date has passed to 'overdue'.
     * Intended to be called from a scheduled command (see console/Kernel).
     */
    public static function flagOverdue(): int
    {
        return MaintenanceSchedule::where('status', 'scheduled')
            ->whereDate('next_maintenance_date', '<', now())
            ->update(['status' => 'overdue']);
    }
}

EOF_CTRL

echo 'Writing Controllers/RepairHistoryController.php'
cat > app/Http/Controllers/RepairHistoryController.php << 'EOF_CTRL'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairHistory;
use App\Models\RepairPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairHistoryController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'reported_date' => 'required|date',
            'issue_description' => 'required|string',
            'maintenance_schedule_id' => 'nullable|exists:maintenance_schedules,id',
            'technician_id' => 'nullable|exists:users,id',
            'parts' => 'nullable|array',
            'parts.*.part_name' => 'required_with:parts|string|max:255',
            'parts.*.quantity' => 'required_with:parts|integer|min:1',
            'parts.*.unit_cost' => 'required_with:parts|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $asset) {
            $repair = RepairHistory::create([
                'asset_id' => $asset->id,
                'maintenance_schedule_id' => $validated['maintenance_schedule_id'] ?? null,
                'reported_date' => $validated['reported_date'],
                'issue_description' => $validated['issue_description'],
                'technician_id' => $validated['technician_id'] ?? null,
                'status' => 'reported',
            ]);

            foreach ($validated['parts'] ?? [] as $part) {
                RepairPart::create([
                    'repair_history_id' => $repair->id,
                    'part_name' => $part['part_name'],
                    'quantity' => $part['quantity'],
                    'unit_cost' => $part['unit_cost'],
                ]);
            }

            // Asset goes into repair status
            $asset->update(['status' => 'under_repair']);
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Repair logged.');
    }

    public function complete(Request $request, RepairHistory $repair)
    {
        $validated = $request->validate([
            'repair_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'downtime_hours' => 'nullable|numeric|min:0',
            'technician_remarks' => 'nullable|string',
            'status' => 'required|in:completed,unrepairable',
        ]);

        $repair->update($validated);

        // Restore asset to active if repaired, or mark for disposal if unrepairable
        $repair->asset->update([
            'status' => $validated['status'] === 'completed' ? 'active' : 'for_disposal',
        ]);

        return redirect()->route('assets.show', $repair->asset)->with('success', 'Repair record updated.');
    }
}

EOF_CTRL

echo 'Writing Controllers/ReportController.php'
cat > app/Http/Controllers/ReportController.php << 'EOF_CTRL'
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

EOF_CTRL

echo 'Writing Console/Commands/SendMaintenanceReminders.php'
cat > app/Console/Commands/SendMaintenanceReminders.php << 'EOF_CMD'
<?php

namespace App\Console\Commands;

use App\Models\MaintenanceSchedule;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Console\Command;

class SendMaintenanceReminders extends Command
{
    protected $signature = 'maintenance:remind';
    protected $description = 'Flag overdue maintenance and email technicians about upcoming (7-day) maintenance';

    public function handle(): int
    {
        // 1. Flag anything past due
        $overdueCount = MaintenanceSchedule::where('status', 'scheduled')
            ->whereDate('next_maintenance_date', '<', now())
            ->update(['status' => 'overdue']);

        $this->info("Flagged {$overdueCount} schedule(s) as overdue.");

        // 2. Notify technicians about maintenance due within the next 7 days
        $upcoming = MaintenanceSchedule::with(['asset', 'technician'])
            ->whereIn('status', ['scheduled', 'overdue'])
            ->whereDate('next_maintenance_date', '<=', now()->addDays(7))
            ->get();

        foreach ($upcoming as $schedule) {
            if ($schedule->technician) {
                $schedule->technician->notify(new MaintenanceDueNotification($schedule));
            }
        }

        $this->info("Sent {$upcoming->count()} reminder notification(s).");

        return self::SUCCESS;
    }
}

EOF_CMD

echo 'Writing Notifications/MaintenanceDueNotification.php'
cat > app/Notifications/MaintenanceDueNotification.php << 'EOF_NOTIF'
<?php

namespace App\Notifications;

use App\Models\MaintenanceSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceDueNotification extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceSchedule $schedule) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $asset = $this->schedule->asset;

        return (new MailMessage)
            ->subject("Maintenance Due: {$asset->asset_tag} - {$asset->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A preventive maintenance task is due for {$asset->name} ({$asset->asset_tag}).")
            ->line("Type: {$this->schedule->maintenance_type}")
            ->line("Due date: {$this->schedule->next_maintenance_date->format('M d, Y')}")
            ->action('View Asset', route('assets.show', $asset))
            ->line('Please complete or update this maintenance task in the system.');
    }

    public function toArray($notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'asset_id' => $this->schedule->asset_id,
            'message' => "Maintenance due for {$this->schedule->asset->asset_tag}",
        ];
    }
}

EOF_NOTIF

echo 'All files created successfully!'
