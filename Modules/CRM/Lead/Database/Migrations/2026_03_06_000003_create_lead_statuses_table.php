<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();
            $table->string('stage', 50)->index(); // NEW, CONTACTED, INTERESTED, QUALIFIED, CONVERTED, LOST
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('color', 20)->nullable();
            $table->timestamps();
        });

        // Add status_id to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->uuid('status_id')->nullable()->after('status')->index();
            $table->foreign('status_id')->references('id')->on('lead_statuses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
        Schema::dropIfExists('lead_statuses');
    }
};
