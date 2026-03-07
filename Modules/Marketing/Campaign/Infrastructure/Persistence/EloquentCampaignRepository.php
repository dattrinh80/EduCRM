<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Infrastructure\Persistence;

use Modules\Marketing\Campaign\Domain\Campaign;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Carbon\Carbon;

class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    public function save(Campaign $campaign): void
    {
        CampaignReadModel::updateOrCreate(
            ['id' => $campaign->getId()],
            [
                'name' => $campaign->name,
                'code' => $campaign->code,
                'channel' => $campaign->channel,
                'budget' => $campaign->budget,
                'center_id' => $campaign->centerId,
                'start_date' => $campaign->startDate ? Carbon::instance($campaign->startDate) : null,
                'end_date' => $campaign->endDate ? Carbon::instance($campaign->endDate) : null,
                'is_active' => $campaign->isActive,
                'created_at' => $campaign->createdAt ? Carbon::instance($campaign->createdAt) : now(),
                'updated_at' => $campaign->updatedAt ? Carbon::instance($campaign->updatedAt) : now(),
            ]
        );
    }

    public function findById(string $id): ?Campaign
    {
        $model = CampaignReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByCode(string $code): ?Campaign
    {
        $model = CampaignReadModel::where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function delete(Campaign $campaign): void
    {
        CampaignReadModel::destroy($campaign->getId());
    }

    private function toDomain(CampaignReadModel $model): Campaign
    {
        return new Campaign(
            $model->id,
            $model->name,
            $model->code,
            $model->channel,
            $model->budget ? (float)$model->budget : null,
            $model->center_id,
            $model->start_date ? new \DateTimeImmutable($model->start_date->toDateTimeString()) : null,
            $model->end_date ? new \DateTimeImmutable($model->end_date->toDateTimeString()) : null,
            $model->is_active,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}