<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemAuditLogger
{
    public static function log(string $action, ?string $actorId = null, ?string $targetUserId = null, array $details = []): void
    {
        DB::table('system_audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $actorId ?? auth()->id(),
            'target_user_id' => $targetUserId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'details' => json_encode($details),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
