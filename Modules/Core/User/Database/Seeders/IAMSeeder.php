<?php

declare(strict_types=1);

namespace Modules\Core\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class IAMSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────
        $adminRoleId = (string) Str::uuid();
        $managerRoleId = (string) Str::uuid();
        $staffRoleId = (string) Str::uuid();

        DB::table('roles')->insert([
            ['id' => $adminRoleId, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $managerRoleId, 'name' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => $staffRoleId, 'name' => 'Staff', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Permissions ────────────────────────────────────
        $permissions = [
            // Users
            'users.view', 'users.create', 'users.update', 'users.delete',
            // Roles
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            // Leads
            'leads.view', 'leads.create', 'leads.update', 'leads.delete', 'leads.export',
            // Centers
            'centers.view', 'centers.create', 'centers.update', 'centers.delete',
            // Students
            'students.view', 'students.create', 'students.update', 'students.delete',
            // Courses
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',
            // Finance
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.delete',
            // Lead Sources
            'lead_sources.view', 'lead_sources.create', 'lead_sources.update', 'lead_sources.delete',
            // Interest Types
            'interest_types.view', 'interest_types.create', 'interest_types.update', 'interest_types.delete',
            // Customers
            'customers.view', 'customers.create', 'customers.update', 'customers.delete', 'customers.export',
            // Tasks
            'tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete',
            // Campaigns
            'campaigns.view', 'campaigns.create', 'campaigns.update', 'campaigns.delete',
        ];

        $permissionIds = [];
        foreach ($permissions as $perm) {
            $existing = DB::table('permissions')->where('name', $perm)->first();
            if ($existing) {
                $permissionIds[$perm] = $existing->id;
            } else {
                $id = (string) Str::uuid();
                $permissionIds[$perm] = $id;
                DB::table('permissions')->insert([
                    'id' => $id,
                    'name' => $perm,
                ]);
            }
        }

        // ── Role → Permissions (Admin gets ALL) ────────────
        foreach ($permissionIds as $permId) {
            if (!DB::table('role_permissions')->where('role_id', $adminRoleId)->where('permission_id', $permId)->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permId,
                ]);
            }
        }

        // Manager gets view + create + update + export on leads/students/courses
        $managerPerms = [
            'leads.view', 'leads.create', 'leads.update', 'leads.export',
            'students.view', 'students.create', 'students.update',
            'courses.view', 'courses.create', 'courses.update',
        ];
        foreach ($managerPerms as $perm) {
            if (!DB::table('role_permissions')->where('role_id', $managerRoleId)->where('permission_id', $permissionIds[$perm])->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $managerRoleId,
                    'permission_id' => $permissionIds[$perm],
                ]);
            }
        }

        // Staff gets view only
        $staffPerms = ['leads.view', 'students.view', 'courses.view'];
        foreach ($staffPerms as $perm) {
            if (!DB::table('role_permissions')->where('role_id', $staffRoleId)->where('permission_id', $permissionIds[$perm])->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $staffRoleId,
                    'permission_id' => $permissionIds[$perm],
                ]);
            }
        }

        // ── Admin User ─────────────────────────────────────
        $adminUserId = (string) Str::uuid();

        DB::table('users')->insert([
            'id' => $adminUserId,
            'name' => 'Admin',
            'email' => 'admin@educrm.vn',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Assign Admin Role with Global Scope ────────────
        DB::table('user_roles')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $adminUserId,
            'role_id' => $adminRoleId,
            'scope_type' => 'SYSTEM',
            'scope_id' => null,
        ]);
    }
}
