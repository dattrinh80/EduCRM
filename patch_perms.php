<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$groups = [
    ['name' => 'Source Management', 'description' => 'Quản lý Nguồn khách hàng', 'sort_order' => 8, 'prefix' => 'sources'],
    ['name' => 'Interest Type Management', 'description' => 'Quản lý Nhu cầu khách hàng', 'sort_order' => 9, 'prefix' => 'interest_types'],
];

foreach ($groups as $group) {
    $existingGroup = DB::table('permission_groups')->where('name', $group['name'])->first();
    if (!$existingGroup) {
        $groupId = (string) Str::uuid();
        $prefix = $group['prefix'];
        unset($group['prefix']);

        DB::table('permission_groups')->insert(array_merge($group, [
            'id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    } else {
        $groupId = $existingGroup->id;
        $prefix = $group['prefix'];
    }

    DB::table('permissions')
        ->where('name', 'like', "{$prefix}.%")
        ->update(['group_id' => $groupId]);
}

$descriptions = [
    'sources.view' => 'Xem danh sách Nguồn',
    'sources.create' => 'Thêm Nguồn mới',
    'sources.update' => 'Sửa Nguồn',
    'sources.delete' => 'Xoá Nguồn',
    'interest_types.view' => 'Xem ds Nhu cầu',
    'interest_types.create' => 'Thêm Nhu cầu mới',
    'interest_types.update' => 'Sửa Nhu cầu',
    'interest_types.delete' => 'Xoá Nhu cầu',
];

foreach ($descriptions as $name => $desc) {
    if (!DB::table('permissions')->where('name', $name)->exists()) {
        DB::table('permissions')->insert([
            'id' => (string) Str::uuid(),
            'name' => $name,
            'description' => $desc,
        ]);
    }
}

$adminRole = DB::table('roles')->where('name', 'Admin')->first();
if ($adminRole) {
    foreach ($descriptions as $name => $desc) {
        $perm = DB::table('permissions')->where('name', $name)->first();
        if ($perm) {
            if (!DB::table('role_permissions')->where('role_id', $adminRole->id)->where('permission_id', $perm->id)->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $perm->id,
                ]);
            }
        }
    }
}
echo "Permissions patched successfully.\n";
