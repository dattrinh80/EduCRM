<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissionId = \Illuminate\Support\Str::uuid()->toString();
        \Illuminate\Support\Facades\DB::table('permissions')->insert([
            'id' => $permissionId,
            'name' => 'leads.export',
        ]);

        $roles = ['Admin', 'Manager', 'SYSTEM_OWNER'];
        foreach ($roles as $roleName) {
            $roleId = \Illuminate\Support\Facades\DB::table('roles')->where('name', $roleName)->value('id');
            if ($roleId) {
                \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = \Illuminate\Support\Facades\DB::table('permissions')->where('name', 'leads.export')->value('id');
        if ($permissionId) {
            \Illuminate\Support\Facades\DB::table('role_permissions')->where('permission_id', $permissionId)->delete();
            \Illuminate\Support\Facades\DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
