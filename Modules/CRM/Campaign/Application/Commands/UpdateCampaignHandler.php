<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Commands;
use Modules\CRM\Campaign\Domain\CampaignRepositoryInterface;
class UpdateCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(UpdateCampaignCommand $command): void
    {
        $campaign = $this->repository->findById($command->id);
        if (!$campaign) {
            throw new \Exception("Campaign not found.");
        }
        if ($command->code && $command->code !== $campaign->code && $this->repository->findByCode($command->code)) {
            throw new \Exception("Campaign with this code already exists.");
        }
        $campaign->update(
            $command->name,
            $command->code,
            $command->channel,
            $command->budget,
            $command->startDate,
            $command->endDate,
            $command->isActive
        );
        $this->repository->save($campaign);
    }
}