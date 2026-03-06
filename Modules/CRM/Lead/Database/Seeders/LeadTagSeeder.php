<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Hot', 'color' => 'red'],
            ['name' => 'Warm', 'color' => 'orange'],
            ['name' => 'Cold', 'color' => 'blue'],
            ['name' => 'VIP', 'color' => 'purple'],
            ['name' => 'English', 'color' => 'emerald'],
            ['name' => 'Math', 'color' => 'amber'],
            ['name' => 'High Potential', 'color' => 'pink'],
        ];

        foreach ($tags as $tag) {
            DB::table('lead_tags')->updateOrInsert(
                ['name' => $tag['name']],
                array_merge($tag, [
                    'id' => (string) Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
