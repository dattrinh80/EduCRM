<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class InterestTypeReadModel extends Model
{
    protected $table = 'interest_types';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
