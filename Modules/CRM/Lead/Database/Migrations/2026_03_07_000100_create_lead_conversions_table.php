<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id');
            $table->uuid('student_id');
            $table->uuid('converted_by')->nullable();
            $table->timestamp('converted_at')->useCurrent();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('converted_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['lead_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_conversions');
    }
};
