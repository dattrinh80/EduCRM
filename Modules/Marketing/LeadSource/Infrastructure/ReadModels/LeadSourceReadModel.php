<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class LeadSourceReadModel extends Model
{

    protected $table = 'lead_sources';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'code',
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
