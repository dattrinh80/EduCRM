<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionReadModel extends Model
{
    protected $table = 'permissions';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;

    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroupReadModel::class, 'group_id', 'id');
    }
}
