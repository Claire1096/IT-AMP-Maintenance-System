<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_tag')->unique();
            $table->string('name');
            $table->enum('category', ['furniture', 'office_supplies', 'appliance', 'fixture', 'other']);
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('condition', ['good', 'fair', 'poor'])->default('good');
            $table->enum('status', ['in_use', 'in_storage', 'damaged', 'disposed'])->default('in_use');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_items');
    }
};