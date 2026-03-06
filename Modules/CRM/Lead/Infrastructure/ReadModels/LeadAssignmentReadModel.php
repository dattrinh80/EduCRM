<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class LeadAssignmentReadModel extends Model
{
    protected $table = 'lead_assignments';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'lead_id',
        'assigned_to',
        'assigned_by',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(LeadReadModel::class, 'lead_id');
    }

    public function assignedToUser()
    {
        return $this->belongsTo(UserReadModel::class, 'assigned_to');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(UserReadModel::class, 'assigned_by');
    }
}
