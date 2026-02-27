<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Create center permissions
        $permissions = [
            ['name' => 'centers.view', 'description' => 'Xem danh sách cơ sở'],
            ['name' => 'centers.create', 'description' => 'Tạo cơ sở mới'],
            ['name' => 'centers.update', 'description' => 'Chỉnh sửa cơ sở'],
            ['name' => 'centers.delete', 'description' => 'Xoá cơ sở'],
        ];

        foreach ($permissions as $perm) {
            // Only insert if not exists
            if (!DB::table('permissions')->where('name', $perm['name'])->exists()) {
                DB::table('permissions')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $perm['name'],
                    'description' => $perm['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create permission group for centers
        if (!DB::table('permission_groups')->where('name', 'Center Management')->exists()) {
            $groupId = (string) Str::uuid();
            DB::table('permission_groups')->insert([
                'id' => $groupId,
                'name' => 'Center Management',
                'description' => 'Quản lý cơ sở / chi nhánh',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link permissions to group
            DB::table('permissions')
                ->where('name', 'like', 'centers.%')
                ->update(['group_id' => $groupId]);
        }

        // Grant all center permissions to admin role (role with id that has all permissions)
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $centerPermissions = DB::table('permissions')
                ->where('name', 'like', 'centers.%')
                ->pluck('id');

            foreach ($centerPermissions as $permId) {
                if (!DB::table('role_permissions')->where('role_id', $adminRole->id)->where('permission_id', $permId)->exists()) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Remove center permissions from role_permissions
        $permIds = DB::table('permissions')
            ->where('name', 'like', 'centers.%')
            ->pluck('id');

        DB::table('role_permissions')->whereIn('permission_id', $permIds)->delete();

        // Remove permission group
        DB::table('permission_groups')->where('name', 'Center Management')->delete();

        // Remove permissions
        DB::table('permissions')->where('name', 'like', 'centers.%')->delete();
    }
};
