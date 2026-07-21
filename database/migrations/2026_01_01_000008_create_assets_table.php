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

