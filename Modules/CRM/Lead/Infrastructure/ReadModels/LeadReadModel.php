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
        'status_id',
        'center_id',
        'dob',
        'gender',
        'lead_source_id',
        'campaign_id',
        'interest_type_id',
        'assigned_to'
    ];

    public function leadStatus()
    {
        return $this->belongsTo(\Modules\CRM\LeadStatus\Infrastructure\ReadModels\LeadStatusReadModel::class, 'status_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(\Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel::class, 'lead_source_id');
    }

    public function campaign()
    {
        return $this->belongsTo(\Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel::class, 'campaign_id');
    }

    public function interestType()
    {
        return $this->belongsTo(\Modules\Marketing\InterestType\Infrastructure\ReadModels\InterestTypeReadModel::class, 'interest_type_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'assigned_to');
    }

    public function center()
    {
        return $this->belongsTo(\Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel::class, 'center_id');
    }

    public function activities()
    {
        return $this->hasMany(\Modules\CRM\LeadActivity\Infrastructure\ReadModels\LeadActivityReadModel::class, 'lead_id');
    }

    public function notes()
    {
        return $this->hasMany(\Modules\CRM\LeadNote\Infrastructure\ReadModels\LeadNoteReadModel::class, 'lead_id');
    }

    public function tags()
    {
        return $this->belongsToMany(\Modules\CRM\LeadTag\Infrastructure\ReadModels\LeadTagReadModel::class, 'lead_tag_pivot', 'lead_id', 'tag_id');
    }

    public function assignments()
    {
        return $this->hasMany(LeadAssignmentReadModel::class, 'lead_id')->latest();
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

