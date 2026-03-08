<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\ReadModels;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class UserReadModel extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected $hidden = ['password'];

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRoleReadModel::class, 'user_id', 'id');
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RoleReadModel::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Check if this user has the given role name.
     */

    public function hasRole(string $roleName): bool
    {
        return $this->userRoles()
            ->whereHas('role', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            })
            ->exists();
    }
}
