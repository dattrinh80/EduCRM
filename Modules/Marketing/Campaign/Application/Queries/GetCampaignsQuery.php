<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Queries;
class GetCampaignsQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?bool $isActive = null,
        public readonly int $perPage = 20,
        public readonly int $page = 1,
        public readonly ?string $sortBy = null,
        public readonly string $sortDirection = 'desc',
        public readonly ?float $budgetFrom = null,
        public readonly ?float $budgetTo = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly ?string $centerId = null
    ) {}
}