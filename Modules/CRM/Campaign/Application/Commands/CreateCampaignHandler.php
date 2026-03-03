<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Commands;
use Modules\CRM\Campaign\Domain\Campaign;
use Modules\CRM\Campaign\Domain\CampaignRepositoryInterface;
class CreateCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(CreateCampaignCommand $command): string
    {
        if ($command->code && $this->repository->findByCode($command->code)) {
            throw new \Exception("Campaign with this code already exists.");
        }
        $campaign = Campaign::create(
            $command->name,
            $command->code,
            $command->channel,
            $command->budget,
            $command->centerId,
            $command->startDate,
            $command->endDate
        );
        $this->repository->save($campaign);
        return $campaign->getId();
    }
}