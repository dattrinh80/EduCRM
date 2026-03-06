<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadStatus\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class LeadStatusReadModel extends Model
{
    protected $table = 'lead_statuses';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'stage',
        'sort_order',
        'is_active',
        'color',
    ];

    public function leads()
    {
        return $this->hasMany(\Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel::class, 'status_id');
    }
}
