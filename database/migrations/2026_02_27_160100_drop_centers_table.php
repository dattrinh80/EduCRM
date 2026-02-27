<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('centers');

        // Clean up any orphaned migration records related to centers
        DB::table('migrations')
            ->where('migration', 'like', '%create_centers_table%')
            ->delete();
    }

    public function down(): void
    {
        // Not reversible — the center module will create its own migration
    }
};
