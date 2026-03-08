<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->string('status')->default('TODO')->index(); // TODO, DOING, DONE, CANCELLED
            $table->string('priority')->default('MEDIUM')->index(); // LOW, MEDIUM, HIGH, URGENT
            
            $table->uuid('assigned_to')->nullable()->index();
            $table->uuid('assigned_by')->index();
            $table->uuid('center_id')->index();

            // Polymorphic relation
            $table->uuid('relation_id')->nullable()->index();
            $table->string('relation_type')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
