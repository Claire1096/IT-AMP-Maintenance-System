<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_monthly_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_monthly_count_id')->constrained()->cascadeOnDelete();
            $table->foreignId('finance_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('expected_quantity');
            $table->unsignedInteger('counted_quantity')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['finance_monthly_count_id', 'finance_item_id'], 'fmci_count_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_monthly_count_items');
    }
};
