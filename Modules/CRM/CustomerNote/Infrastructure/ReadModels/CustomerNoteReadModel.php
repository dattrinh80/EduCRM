<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;

class CustomerNoteReadModel extends Model
{
    protected $table = 'customer_notes';

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'content',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(\Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\Core\User\Infrastructure\ReadModels\UserReadModel::class, 'created_by');
    }
}

