<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Domain;

interface CampaignRepositoryInterface
{
    public function save(Campaign $campaign): void;
    public function findById(string $id): ?Campaign;
    public function findByCode(string $code): ?Campaign;
    public function delete(Campaign $campaign): void;
}