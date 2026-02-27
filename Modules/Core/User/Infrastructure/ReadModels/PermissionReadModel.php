<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class PermissionReadModel extends Model
{
    protected $table = 'permissions';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
}
