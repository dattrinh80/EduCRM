<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'User Management', 'description' => 'Quản lý tài khoản người dùng', 'sort_order' => 1, 'prefix' => 'users'],
            ['name' => 'Role Management', 'description' => 'Quản lý vai trò và phân quyền', 'sort_order' => 2, 'prefix' => 'roles'],
            ['name' => 'Lead Management', 'description' => 'Quản lý khách hàng tiềm năng', 'sort_order' => 3, 'prefix' => 'leads'],
            ['name' => 'Center Management', 'description' => 'Quản lý cơ sở / chi nhánh', 'sort_order' => 4, 'prefix' => 'centers'],
            ['name' => 'Student Management', 'description' => 'Quản lý học viên', 'sort_order' => 5, 'prefix' => 'students'],
            ['name' => 'Course Management', 'description' => 'Quản lý khoá học', 'sort_order' => 6, 'prefix' => 'courses'],
            ['name' => 'Finance', 'description' => 'Quản lý tài chính, hoá đơn', 'sort_order' => 7, 'prefix' => 'invoices'],
        ];

        foreach ($groups as $group) {
            $groupId = (string) Str::uuid();
            $prefix = $group['prefix'];
            unset($group['prefix']);

            DB::table('permission_groups')->insert(array_merge($group, [
                'id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Link existing permissions to group
            DB::table('permissions')
                ->where('name', 'like', "{$prefix}.%")
                ->update(['group_id' => $groupId]);
        }

        // Add descriptions to permissions
        $descriptions = [
            'users.view' => 'Xem danh sách người dùng',
            'users.create' => 'Tạo người dùng mới',
            'users.update' => 'Chỉnh sửa người dùng',
            'users.delete' => 'Xoá người dùng',
            'roles.view' => 'Xem danh sách vai trò',
            'roles.create' => 'Tạo vai trò mới',
            'roles.update' => 'Chỉnh sửa vai trò',
            'roles.delete' => 'Xoá vai trò',
            'leads.view' => 'Xem danh sách Lead',
            'leads.create' => 'Tạo Lead mới',
            'leads.update' => 'Chỉnh sửa Lead',
            'leads.delete' => 'Xoá Lead',
            'centers.view' => 'Xem danh sách cơ sở',
            'centers.create' => 'Tạo cơ sở mới',
            'centers.update' => 'Chỉnh sửa cơ sở',
            'centers.delete' => 'Xoá cơ sở',
            'students.view' => 'Xem danh sách học viên',
            'students.create' => 'Thêm học viên mới',
            'students.update' => 'Chỉnh sửa học viên',
            'students.delete' => 'Xoá học viên',
            'courses.view' => 'Xem danh sách khoá học',
            'courses.create' => 'Tạo khoá học mới',
            'courses.update' => 'Chỉnh sửa khoá học',
            'courses.delete' => 'Xoá khoá học',
            'invoices.view' => 'Xem danh sách hoá đơn',
            'invoices.create' => 'Tạo hoá đơn mới',
            'invoices.update' => 'Chỉnh sửa hoá đơn',
            'invoices.delete' => 'Xoá hoá đơn',
        ];

        foreach ($descriptions as $name => $desc) {
            DB::table('permissions')->where('name', $name)->update(['description' => $desc]);
        }
    }
}
