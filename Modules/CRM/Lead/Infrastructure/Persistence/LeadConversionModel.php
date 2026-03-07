<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

class LeadConversionModel extends Model
{
    protected $table = 'lead_conversions';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'lead_id',
        'student_id',
        'converted_by',
        'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];
}
