<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->index();
            $table->string('activity_type', 50)->index(); // call, meeting, sms, email, note, status_change, assignment, conversion
            $table->text('description')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_activities');
    }
};
