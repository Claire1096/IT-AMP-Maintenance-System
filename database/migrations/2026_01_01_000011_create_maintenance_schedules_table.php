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

