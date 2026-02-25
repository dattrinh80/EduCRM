<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class LeadReadModel extends Model
{
    protected $table = 'leads';

    // Disabling incrementing since we use UUID
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'status',
        'center_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
