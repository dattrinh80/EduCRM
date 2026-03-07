<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
class DeleteCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(DeleteCampaignCommand $command): void
    {
        $campaign = $this->repository->findById($command->id);
        if (!$campaign) {
            throw new \Exception("Campaign not found.");
        }
        $this->repository->delete($campaign);
    }
}