<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_tag')->unique();
            $table->string('name');
            $table->string('asset_type')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('current_quantity')->default(0);
            $table->unsignedInteger('missing_quantity')->default(0);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('in_use');
            $table->timestamp('missing_since')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_items');
    }
};
