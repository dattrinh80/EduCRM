<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to roles
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_system_role')->default(false);
        });

        // Add columns to permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_system_permission')->default(false);
        });

        // Create system_audit_logs
        Schema::create('system_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('actor_id')->nullable();
            $table->uuid('target_user_id')->nullable();
            $table->string('action');
            $table->string('ip_address')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });

        DB::transaction(function () {
            // New MANAGE_SYSTEM_OWNER permission
            $permId = (string) Str::uuid();
            DB::table('permissions')->insert([
                'id' => $permId,
                'name' => 'MANAGE_SYSTEM_OWNER',
                'is_system_permission' => true,
            ]);

            // Create SYSTEM_OWNER role
            $systemOwnerRoleId = (string) Str::uuid();
            DB::table('roles')->insert([
                'id' => $systemOwnerRoleId,
                'name' => 'SYSTEM_OWNER',
                'is_system_role' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign all current permissions to SYSTEM_OWNER
            $allPermissions = DB::table('permissions')->pluck('id')->toArray();
            $rolePerms = [];
            foreach ($allPermissions as $pId) {
                $rolePerms[] = [
                    'role_id' => $systemOwnerRoleId,
                    'permission_id' => $pId,
                ];
            }
            DB::table('role_permissions')->insert($rolePerms);

            // Create or get root user
            $rootUser = DB::table('users')->where('email', 'root@system.local')->first();
            if (!$rootUser) {
                $rootUserId = (string) Str::uuid();
                DB::table('users')->insert([
                    'id' => $rootUserId,
                    'name' => 'System Owner',
                    'email' => 'root@system.local',
                    'password' => \Illuminate\Support\Facades\Hash::make('Str0ngP@ssw0rd!'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $rootUserId = $rootUser->id;
            }

            // Assign SYSTEM_OWNER at SYSTEM scope
            DB::table('user_roles')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $rootUserId,
                'role_id' => $systemOwnerRoleId,
                'scope_type' => 'SYSTEM',
                'scope_id' => null,
            ]);
            
            // Also assign SYSTEM_OWNER to existing admin@admin.com, admin@educrm.vn, admin@eim.vn so they don't lose bypass
            $oldRoots = DB::table('users')->whereIn('email', ['admin@admin.com', 'admin@educrm.vn', 'admin@eim.vn'])->get();
            foreach ($oldRoots as $oldRoot) {
                $exists = DB::table('user_roles')->where([
                    'user_id' => $oldRoot->id,
                    'role_id' => $systemOwnerRoleId,
                    'scope_type' => 'SYSTEM',
                ])->exists();
                if (!$exists) {
                     DB::table('user_roles')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => $oldRoot->id,
                        'role_id' => $systemOwnerRoleId,
                        'scope_type' => 'SYSTEM',
                        'scope_id' => null,
                    ]);
                }
            }

            // Verify count
            $count = DB::table('user_roles')
                ->where('role_id', $systemOwnerRoleId)
                ->where('scope_type', 'SYSTEM')
                ->count();
                
            if ($count === 0) {
                throw new \Exception("Migration failed: SYSTEM_OWNER assignment is empty.");
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_audit_logs');
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_system_role');
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('is_system_permission');
        });
    }
};
