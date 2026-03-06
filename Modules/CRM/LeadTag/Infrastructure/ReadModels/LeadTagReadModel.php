<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeadTagReadModel extends Model
{
    use HasUuids;

    protected $table = 'lead_tags';
    protected $fillable = ['id', 'name', 'color'];

    public function leads()
    {
        return $this->belongsToMany(\Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel::class, 'lead_tag_pivot', 'tag_id', 'lead_id');
    }
}

