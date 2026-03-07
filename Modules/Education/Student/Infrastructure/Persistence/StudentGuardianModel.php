<?php

declare(strict_types=1);

namespace Modules\Education\Student\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentGuardianModel extends Model
{
    protected $table = 'student_guardians';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'student_id',
        'guardian_id',
        'relationship',
        'is_primary',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
