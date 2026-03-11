<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomerTagReadModel extends Model
{
    use HasUuids;

    protected $table = 'customer_tags';
    protected $fillable = ['id', 'name', 'color'];

    public function customers()
    {
        return $this->belongsToMany(\Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel::class, 'customer_tag_pivot', 'tag_id', 'customer_id');
    }
}

