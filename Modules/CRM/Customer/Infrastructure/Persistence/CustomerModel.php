<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Persistence\Traits\BelongsToCenter;

class CustomerModel extends Model
{
    use SoftDeletes, BelongsToCenter;

    protected $table = 'customers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'dob',
        'gender',
        'address',
        'center_id',
    ];

    public function tags()
    {
        return $this->belongsToMany(\Modules\CRM\CustomerTag\Infrastructure\ReadModels\CustomerTagReadModel::class, 'customer_tag_pivot', 'customer_id', 'tag_id');
    }

    public function notes()
    {
        return $this->hasMany(\Modules\CRM\CustomerNote\Infrastructure\ReadModels\CustomerNoteReadModel::class, 'customer_id')->latest();
    }

    public function activities()
    {
        return $this->hasMany(\Modules\CRM\CustomerActivity\Infrastructure\ReadModels\CustomerActivityReadModel::class, 'customer_id')->latest();
    }

    public function studentGuardians()
    {
        return $this->hasMany(\Modules\Education\Student\Infrastructure\Persistence\StudentGuardianModel::class, 'guardian_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(\Modules\Education\Student\Infrastructure\Persistence\StudentModel::class, 'customer_id');
    }
}
