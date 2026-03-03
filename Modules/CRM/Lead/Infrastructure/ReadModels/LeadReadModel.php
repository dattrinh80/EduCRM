<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Persistence\Traits\BelongsToCenter;

class LeadReadModel extends Model
{
    use BelongsToCenter;

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
        'center_id',
        'dob',
        'source_id',
        'campaign_id',
        'interest_type_id',
        'assigned_to'
    ];

    public function source()
    {
        return $this->belongsTo(\Modules\CRM\Source\Infrastructure\ReadModels\SourceReadModel::class, 'source_id');
    }

    public function interestType()
    {
        return $this->belongsTo(\Modules\CRM\InterestType\Infrastructure\ReadModels\InterestTypeReadModel::class, 'interest_type_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'assigned_to');
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
