<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\User\Database\Seeders\IAMSeeder;
use Modules\Core\Permission\Database\Seeders\PermissionGroupSeeder;
use Modules\CRM\Lead\Database\Seeders\LeadStatusSeeder;
use Modules\CRM\Lead\Database\Seeders\LeadTagSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IAMSeeder::class,
            PermissionGroupSeeder::class,
            AssignAdminPermissionsSeeder::class,
            LeadStatusSeeder::class,
            LeadTagSeeder::class,
        ]);
    }
}
