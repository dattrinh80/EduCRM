<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use InvalidArgumentException;

class MergeLeadsHandler
{
    private LeadRepositoryInterface $repository;
    private \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository;

    public function __construct(
        LeadRepositoryInterface $repository,
        \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository
    ) {
        $this->repository = $repository;
        $this->statusRepository = $statusRepository;
    }

    public function handle(MergeLeadsCommand $command): void
    {
        if (empty($command->slaveLeadIds)) {
            return;
        }

        $masterLead = $this->repository->findById($command->masterLeadId);
        if (!$masterLead) {
            throw new InvalidArgumentException("Master lead not found.");
        }

        $mergedStatus = $this->statusRepository->findByName('Merged');
        if (!$mergedStatus) {
            throw new \Exception("Status 'Merged' not found. Please run LeadStatusSeeder.");
        }

        foreach ($command->slaveLeadIds as $slaveId) {
            if ($slaveId === $command->masterLeadId) continue;
            
            $slaveLead = $this->repository->findById($slaveId);
            if ($slaveLead) {
                $currentStatus = $this->statusRepository->findById($slaveLead->statusId);
                // Đổi trạng thái lead trùng thành 'merged'
                $slaveLead->setStatus($mergedStatus->id, $mergedStatus->stage, $currentStatus?->stage);
                $this->repository->update($slaveLead);
            }
        }
    }
}
