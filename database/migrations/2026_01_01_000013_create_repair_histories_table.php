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

