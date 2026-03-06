<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('sources', 'lead_sources');
        
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('source_id', 'lead_source_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('lead_source_id', 'source_id');
        });

        Schema::rename('lead_sources', 'sources');
    }
};
