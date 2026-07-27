<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_items', function (Blueprint $table) {
            $table->string('building_structure')->nullable()->after('asset_type');
        });
    }

    public function down(): void
    {
        Schema::table('facility_items', function (Blueprint $table) {
            $table->dropColumn('building_structure');
        });
    }
};