<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Infrastructure\Persistence;

use Modules\CRM\LeadTag\Domain\LeadTag;
use Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface;
use Modules\CRM\LeadTag\Infrastructure\ReadModels\LeadTagReadModel;
use Illuminate\Support\Facades\DB;

class EloquentLeadTagRepository implements LeadTagRepositoryInterface
{
    public function save(LeadTag $tag): void
    {
        LeadTagReadModel::updateOrCreate(
            ['id' => $tag->getId()],
            [
                'name' => $tag->name,
                'color' => $tag->color,
            ]
        );
    }

    public function findById(string $id): ?LeadTag
    {
        $model = LeadTagReadModel::find($id);
        if (!$model) return null;

        return new LeadTag($model->id, $model->name, $model->color);
    }

    public function findByName(string $name): ?LeadTag
    {
        $model = LeadTagReadModel::where('name', $name)->first();
        if (!$model) return null;

        return new LeadTag($model->id, $model->name, $model->color);
    }

    public function getAll(): array
    {
        return LeadTagReadModel::orderBy('name')
            ->get()
            ->map(fn($m) => new LeadTag($m->id, $m->name, $m->color))
            ->toArray();
    }

    public function delete(string $id): void
    {
        LeadTagReadModel::destroy($id);
    }

    public function syncTagsForLead(string $leadId, array $tagIds): void
    {
        DB::table('lead_tag_pivot')->where('lead_id', $leadId)->delete();
        
        if (empty($tagIds)) return;

        $data = array_map(fn($tagId) => [
            'lead_id' => $leadId,
            'tag_id' => $tagId
        ], $tagIds);

        DB::table('lead_tag_pivot')->insert($data);
    }
}

