<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('color', 20)->nullable()->default('#6366f1');
            $table->timestamps();
        });

        Schema::create('customer_tag_pivot', function (Blueprint $table) {
            $table->uuid('customer_id');
            $table->uuid('tag_id');
            $table->primary(['customer_id', 'tag_id']);

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('customer_tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tag_pivot');
        Schema::dropIfExists('customer_tags');
    }
};
