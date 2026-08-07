<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_monthly_counts', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->string('status')->default('open'); // open | closed
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_monthly_counts');
    }
};
