<?php

declare(strict_types=1);

namespace Modules\CRM\LeadNote\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class LeadNoteReadModel extends Model
{
    protected $table = 'lead_notes';

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'lead_id',
        'content',
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

