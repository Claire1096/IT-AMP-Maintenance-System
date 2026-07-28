<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->string('category'); // Facility and Maintenance | Fixed Asset Inventory
            $table->foreignId('facility_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asset_name')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('asset_tag_no')->nullable();
            $table->date('date_reported');
            $table->date('date_of_incident')->nullable();
            $table->time('time_of_incident')->nullable();
            $table->string('type_of_incident')->nullable();
            $table->string('cause_of_damage')->nullable();
            $table->string('cause_other_note')->nullable();
            $table->text('description')->nullable();
            $table->string('action_taken')->nullable();
            $table->string('inspected_by')->nullable();
            $table->date('inspection_date')->nullable();
            $table->string('condition')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('facilitator_name')->nullable();
            $table->date('facilitator_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};