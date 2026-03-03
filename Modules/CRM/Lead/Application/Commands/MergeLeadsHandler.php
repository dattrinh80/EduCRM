<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use InvalidArgumentException;

class MergeLeadsHandler
{
    private LeadRepositoryInterface $repository;

    public function __construct(LeadRepositoryInterface $repository)
    {
        $this->repository = $repository;
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

        foreach ($command->slaveLeadIds as $slaveId) {
            if ($slaveId === $command->masterLeadId) continue;
            
            $slaveLead = $this->repository->findById($slaveId);
            if ($slaveLead) {
                // Đổi trạng thái lead trùng thành 'merged'
                $slaveLead->status = 'merged';
                $slaveLead->updatedAt = new \DateTimeImmutable();
                $this->repository->update($slaveLead);
            }
        }
    }
}
