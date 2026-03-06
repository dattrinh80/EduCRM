<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'New', 'stage' => 'NEW', 'sort_order' => 1, 'color' => 'blue'],
            ['name' => 'Contacted', 'stage' => 'CONTACTED', 'sort_order' => 2, 'color' => 'amber'],
            ['name' => 'Interested', 'stage' => 'INTERESTED', 'sort_order' => 3, 'color' => 'purple'],
            ['name' => 'Qualified', 'stage' => 'QUALIFIED', 'sort_order' => 4, 'color' => 'emerald'],
            ['name' => 'Converted', 'stage' => 'CONVERTED', 'sort_order' => 5, 'color' => 'green'],
            ['name' => 'Lost', 'stage' => 'LOST', 'sort_order' => 6, 'color' => 'red'],
            ['name' => 'Merged', 'stage' => 'LOST', 'sort_order' => 7, 'color' => 'slate'],
        ];

        foreach ($statuses as $status) {
            $existing = DB::table('lead_statuses')->where('name', $status['name'])->first();
            if (!$existing) {
                DB::table('lead_statuses')->insert(
                    array_merge($status, [
                        'id' => (string) Str::uuid(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            } else {
                DB::table('lead_statuses')->where('id', $existing->id)->update(
                    array_merge($status, [
                        'updated_at' => now(),
                    ])
                );
            }
        }

        // Migrate existing leads from `status` (string) to `status_id` (UUID)
        $leads = DB::table('leads')->whereNull('status_id')->get();
        foreach ($leads as $lead) {
            $matchingStatus = DB::table('lead_statuses')->where('name', $lead->status)->first();
            if ($matchingStatus) {
                DB::table('leads')->where('id', $lead->id)->update(['status_id' => $matchingStatus->id]);
            }
        }
    }
}
