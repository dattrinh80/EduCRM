<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignAdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Find Admin role
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        if (!$adminRole) {
            echo "Admin role not found!\n";
            return;
        }

        // 2. We want to add these new permissions
        $permissions = [
            'customers.view', 'customers.create', 'customers.update', 'customers.delete', 'customers.export',
            'tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete',
            'campaigns.view', 'campaigns.create', 'campaigns.update', 'campaigns.delete'
        ];

        // 3. For each permission, get its ID from `permissions` table and insert into `role_permissions`
        foreach ($permissions as $permName) {
            $perm = DB::table('permissions')->where('name', $permName)->first();
            
            if ($perm) {
                // Check if already assigned
                $exists = DB::table('role_permissions')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $perm->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $perm->id,
                    ]);
                    echo "Assigned {$permName} to Admin\n";
                } else {
                    echo "{$permName} already assigned to Admin\n";
                }
            } else {
                echo "Permission {$permName} not found in database!\n";
            }
        }
    }
}
