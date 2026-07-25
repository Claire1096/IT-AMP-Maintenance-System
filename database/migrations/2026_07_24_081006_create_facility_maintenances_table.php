<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_item_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->enum('status', ['pending', 'done', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('completed_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_maintenances');
    }
};