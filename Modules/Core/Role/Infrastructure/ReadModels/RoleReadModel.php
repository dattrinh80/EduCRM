<?php

declare(strict_types=1);

namespace Modules\Core\Role\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionReadModel;

class RoleReadModel extends Model
{
    protected $table = 'roles';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionReadModel::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }
}
