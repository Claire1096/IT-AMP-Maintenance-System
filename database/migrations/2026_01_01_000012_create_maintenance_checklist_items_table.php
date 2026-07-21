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

