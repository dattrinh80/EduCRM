<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('email');
            $table->uuid('source_id')->nullable()->after('status');
            $table->uuid('campaign_id')->nullable()->after('source_id');
            $table->uuid('interest_type_id')->nullable()->after('campaign_id');
            $table->uuid('assigned_to')->nullable()->after('interest_type_id');

            // Foreign keys
            $table->foreign('source_id')->references('id')->on('lead_sources')->onDelete('set null');
            $table->foreign('interest_type_id')->references('id')->on('interest_types')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            // Note: campaigns table might not exist yet according to our plan, so we skip foreign key for campaign_id for now, 
            // or if it exists we add it. I'll add the foreign key later when Campaign module is finalized.
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->dropForeign(['interest_type_id']);
            $table->dropForeign(['assigned_to']);

            $table->dropColumn([
                'dob',
                'source_id',
                'campaign_id',
                'interest_type_id',
                'assigned_to',
            ]);
        });
    }
};
