<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleReadModel extends Model
{
    protected $table = 'user_roles';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;

    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleReadModel::class, 'role_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserReadModel::class, 'user_id', 'id');
    }
}
