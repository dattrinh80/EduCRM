<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class RoleReadModel extends Model
{
    protected $table = 'roles';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
}
