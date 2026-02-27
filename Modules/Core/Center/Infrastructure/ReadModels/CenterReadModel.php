<?php

declare(strict_types=1);

namespace Modules\Core\Center\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class CenterReadModel extends Model
{
    protected $table = 'centers';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
