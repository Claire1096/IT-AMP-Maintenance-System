<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_item_monthly_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_item_id')->constrained()->cascadeOnDelete();
            $table->date('month');
            $table->unsignedInteger('quantity_on_hand');
            $table->unsignedInteger('missing_quantity')->default(0);
            $table->timestamps();

            $table->unique(['finance_item_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_item_monthly_logs');
    }
};
