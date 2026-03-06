<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('color', 20)->nullable()->default('slate');
            $table->timestamps();
        });

        Schema::create('lead_tag_pivot', function (Blueprint $table) {
            $table->uuid('lead_id');
            $table->uuid('tag_id');
            $table->primary(['lead_id', 'tag_id']);
            
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('lead_tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_tag_pivot');
        Schema::dropIfExists('lead_tags');
    }
};
