<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionGroupReadModel extends Model
{
    protected $table = 'permission_groups';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function permissions(): HasMany
    {
        return $this->hasMany(PermissionReadModel::class, 'group_id', 'id');
    }
}
