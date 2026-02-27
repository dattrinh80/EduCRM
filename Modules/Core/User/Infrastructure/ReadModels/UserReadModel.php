<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\ReadModels;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;

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

    public function center(): BelongsTo
    {
        return $this->belongsTo(CenterReadModel::class, 'center_id', 'id');
    }
}
