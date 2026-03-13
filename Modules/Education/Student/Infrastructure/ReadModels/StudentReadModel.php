<?php

declare(strict_types=1);

namespace Modules\Education\Student\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentReadModel extends Model
{
    use SoftDeletes;

    protected $table = 'students';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'student_code',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(\Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel::class, 'customer_id');
    }

    public function guardians()
    {
        return $this->belongsToMany(
            \Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel::class,
            'student_guardians',
            'student_id',
            'guardian_id'
        )->withPivot('relationship', 'is_primary');
    }
}
