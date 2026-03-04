<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('user_roles')
            ->where('scope_type', 'ALL')
            ->update(['scope_type' => 'SYSTEM']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_roles')
            ->where('scope_type', 'SYSTEM')
            ->update(['scope_type' => 'ALL']);
    }
};
