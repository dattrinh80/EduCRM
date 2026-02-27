<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add group_id FK to permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->uuid('group_id')->nullable()->after('name');
            $table->string('description')->nullable()->after('group_id');
            $table->foreign('group_id')->references('id')->on('permission_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id', 'description']);
        });
        Schema::dropIfExists('permission_groups');
    }
};
