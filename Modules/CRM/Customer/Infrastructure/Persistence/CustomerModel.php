<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerModel extends Model
{
    use SoftDeletes;

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
}
