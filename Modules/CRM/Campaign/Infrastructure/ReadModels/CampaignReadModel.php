<?php
declare(strict_types=1);

namespace Modules\CRM\Campaign\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CampaignReadModel extends Model
{
    use HasUuids;

    protected $table = 'campaigns';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'code',
        'channel',
        'budget',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}