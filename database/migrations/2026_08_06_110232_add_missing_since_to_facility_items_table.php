<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_items', function (Blueprint $table) {
            $table->timestamp('missing_since')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('facility_items', function (Blueprint $table) {
            $table->dropColumn('missing_since');
        });
    }
};
