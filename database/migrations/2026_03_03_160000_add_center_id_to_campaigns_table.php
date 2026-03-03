<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->uuid('center_id')->nullable()->after('id');
            $table->foreign('center_id')->references('id')->on('centers')->nullOnDelete();
            $table->index('center_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropIndex(['center_id']);
            $table->dropColumn('center_id');
        });
    }
};
