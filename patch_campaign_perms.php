<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$group = ['name' => 'Campaign Management', 'description' => 'Quản lý Chiến dịch', 'sort_order' => 10, 'prefix' => 'campaigns'];

$existingGroup = DB::table('permission_groups')->where('name', $group['name'])->first();
if (!$existingGroup) {
    $groupId = (string) Str::uuid();
    $prefix = $group['prefix'];
    
    DB::table('permission_groups')->insert([
        'id' => $groupId,
        'name' => 'Campaign Management',
        'description' => 'Quản lý Chiến dịch',
        'sort_order' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} else {
    $groupId = $existingGroup->id;
    $prefix = $group['prefix'];
}

$descriptions = [
    'campaigns.view' => 'Xem ds Chiến dịch',
    'campaigns.create' => 'Thêm Chiến dịch mới',
    'campaigns.update' => 'Sửa Chiến dịch',
    'campaigns.delete' => 'Xoá Chiến dịch',
];

foreach ($descriptions as $name => $desc) {
    if (!DB::table('permissions')->where('name', $name)->exists()) {
        DB::table('permissions')->insert([
            'id' => (string) Str::uuid(),
            'name' => $name,
            'description' => $desc,
            'group_id' => $groupId
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
echo "Campaign permissions patched successfully.\n";