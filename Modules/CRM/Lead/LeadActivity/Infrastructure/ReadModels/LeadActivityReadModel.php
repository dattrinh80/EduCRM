<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class LeadActivityReadModel extends Model
{
    protected $table = 'lead_activities';

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'lead_id',
        'activity_type',
        'description',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(\Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel::class, 'lead_id');
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'created_by');
    }
}
