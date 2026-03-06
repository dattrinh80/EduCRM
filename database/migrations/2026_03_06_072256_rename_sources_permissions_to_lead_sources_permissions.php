<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename permissions
        DB::table('permissions')
            ->where('name', 'like', 'sources.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'sources.', 'lead_sources.')"),
                'description' => DB::raw("REPLACE(description, 'Nguồn', 'Nguồn khách hàng')")
            ]);

        // 2. Rename permission group
        DB::table('permission_groups')
            ->where('name', 'Source Management')
            ->update([
                'name' => 'Lead Source Management',
                'description' => 'Quản lý Nguồn khách hàng'
            ]);
    }

    public function down(): void
    {
        // 1. Rollback permission group
        DB::table('permission_groups')
            ->where('name', 'Lead Source Management')
            ->update([
                'name' => 'Source Management',
                'description' => 'Quản lý Nguồn'
            ]);

        // 2. Rollback permissions
        DB::table('permissions')
            ->where('name', 'like', 'lead_sources.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'lead_sources.', 'sources.')"),
                'description' => DB::raw("REPLACE(description, 'Nguồn khách hàng', 'Nguồn')")
            ]);
    }
};
