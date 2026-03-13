<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Persistence\Traits\BelongsToCenter;
use Modules\CRM\CustomerTag\Infrastructure\ReadModels\CustomerTagReadModel;

class CustomerReadModel extends Model
{
    use SoftDeletes, BelongsToCenter;

    protected $table = 'customers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'dob' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\CRM\CustomerTag\Infrastructure\ReadModels\CustomerTagReadModel::class,
            'customer_tag_pivot',
            'customer_id',
            'tag_id'
        );
    }

    public function studentGuardians(): HasMany
    {
        return $this->hasMany(\Modules\Education\Student\Infrastructure\Persistence\StudentGuardianModel::class, 'guardian_id', 'id');
    }

    public function studentProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\Modules\Education\Student\Infrastructure\Persistence\StudentModel::class, 'customer_id', 'id');
    }
}
