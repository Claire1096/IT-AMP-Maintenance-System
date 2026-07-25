<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_maintenances', function (Blueprint $table) {
            $table->string('maintenance_type')->nullable()->after('facility_item_id');
            $table->string('priority')->nullable()->after('maintenance_type');
            $table->time('scheduled_time')->nullable()->after('due_date');
            $table->string('technician')->nullable()->after('scheduled_time');
            $table->json('checklist')->nullable()->after('technician');
        });
    }

    public function down(): void
    {
        Schema::table('facility_maintenances', function (Blueprint $table) {
            $table->dropColumn(['maintenance_type', 'priority', 'scheduled_time', 'technician', 'checklist']);
        });
    }
};