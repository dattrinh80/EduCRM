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
            ['name' => 'Lead Source Management', 'description' => 'Quản lý Nguồn khách hàng', 'sort_order' => 8, 'prefix' => 'lead_sources'],
            ['name' => 'Interest Type Management', 'description' => 'Quản lý Nhu cầu khách hàng', 'sort_order' => 9, 'prefix' => 'interest_types'],
            // Bổ sung các nhóm quyền mới refactor
            ['name' => 'Customer Management', 'description' => 'Quản lý khách hàng chuẩn', 'sort_order' => 10, 'prefix' => 'customers'],
            ['name' => 'Task Management', 'description' => 'Quản lý nhiệm vụ (Tasks)', 'sort_order' => 11, 'prefix' => 'tasks'],
            ['name' => 'Campaign Management', 'description' => 'Quản lý chiến dịch', 'sort_order' => 12, 'prefix' => 'campaigns'],
        ];

        foreach ($groups as $group) {
            $prefix = $group['prefix'];
            unset($group['prefix']);

            $existingGroup = DB::table('permission_groups')->where('name', $group['name'])->first();

            if (!$existingGroup) {
                $groupId = (string) Str::uuid();
                DB::table('permission_groups')->insert(array_merge($group, [
                    'id' => $groupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $realGroupId = $groupId;
            } else {
                DB::table('permission_groups')->where('id', $existingGroup->id)->update(array_merge($group, [
                    'updated_at' => now(),
                ]));
                $realGroupId = $existingGroup->id;
            }

            // Link existing permissions to group
            DB::table('permissions')
                ->where('name', 'like', "{$prefix}.%")
                ->update(['group_id' => $realGroupId]);
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
            'lead_sources.view' => 'Xem danh sách Nguồn khách hàng',
            'lead_sources.create' => 'Tạo Nguồn khách hàng mới',
            'lead_sources.update' => 'Chỉnh sửa Nguồn khách hàng',
            'lead_sources.delete' => 'Xoá Nguồn khách hàng',
            'interest_types.view' => 'Xem danh sách Nhu cầu khách hàng',
            'interest_types.create' => 'Tạo Nhu cầu mới',
            'interest_types.update' => 'Chỉnh sửa Nhu cầu',
            'interest_types.delete' => 'Xoá Nhu cầu',
            // Quyền mới bổ sung
            'customers.view' => 'Xem danh sách khách hàng chuẩn',
            'customers.create' => 'Tạo khách hàng mới',
            'customers.update' => 'Chỉnh sửa khách hàng',
            'customers.delete' => 'Xoá khách hàng',
            'customers.export' => 'Xuất Excel khách hàng',
            'tasks.view' => 'Xem danh sách nhiệm vụ',
            'tasks.create' => 'Tạo nhiệm vụ mới',
            'tasks.update' => 'Chỉnh sửa/Cập nhật nhiệm vụ',
            'tasks.delete' => 'Xoá nhiệm vụ',
            'campaigns.view' => 'Xem danh sách chiến dịch',
            'campaigns.create' => 'Tạo chiến dịch mới',
            'campaigns.update' => 'Chỉnh sửa chiến dịch',
            'campaigns.delete' => 'Xoá chiến dịch',
        ];

        // Seed raw permissions in `permissions` table before adding description (if they don't exist natively via other commands)
        foreach ($descriptions as $name => $desc) {
            $prefix = explode('.', $name)[0];
            
            // Just use the name mapping.
            $groupNameMap = [
                'users' => 'User Management',
                'roles' => 'Role Management',
                'leads' => 'Lead Management',
                'centers' => 'Center Management',
                'students' => 'Student Management',
                'courses' => 'Course Management',
                'invoices' => 'Finance',
                'lead_sources' => 'Lead Source Management',
                'interest_types' => 'Interest Type Management',
                'customers' => 'Customer Management',
                'tasks' => 'Task Management',
                'campaigns' => 'Campaign Management',
            ];
            
            $realGroupId = null;
            if (isset($groupNameMap[$prefix])) {
                $realGroupId = DB::table('permission_groups')->where('name', $groupNameMap[$prefix])->value('id');
            }

            $permission = DB::table('permissions')->where('name', $name)->first();
            if (!$permission) {
                DB::table('permissions')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                    'description' => $desc,
                    'group_id' => $realGroupId,
                    'is_system_permission' => 0
                ]);
            } else {
                DB::table('permissions')->where('id', $permission->id)->update([
                    'description' => $desc,
                    'group_id' => $realGroupId
                ]);
            }
        }

        foreach ($descriptions as $name => $desc) {
            DB::table('permissions')->where('name', $name)->update(['description' => $desc]);
        }
    }
}
