<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Persistence\Traits\BelongsToCenter;

class TaskReadModel extends Model
{
    use BelongsToCenter;

    protected $table = 'tasks';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'assigned_to',
        'assigned_by',
        'center_id',
        'relation_id',
        'relation_type',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'assigned_by');
    }

    public function center()
    {
        return $this->belongsTo(\Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel::class, 'center_id');
    }

    /**
     * Get the associated model (Lead, Student, etc.)
     */
    public function relation()
    {
        return $this->morphTo('relation', 'relation_type', 'relation_id');
    }
}
