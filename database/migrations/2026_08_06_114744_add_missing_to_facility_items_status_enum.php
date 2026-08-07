<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE facility_items MODIFY COLUMN status ENUM('in_use', 'in_storage', 'damaged', 'disposed', 'missing') NOT NULL DEFAULT 'in_use'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE facility_items MODIFY COLUMN status ENUM('in_use', 'in_storage', 'damaged', 'disposed') NOT NULL DEFAULT 'in_use'");
    }
};
