<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Commands;
class CreateCampaignCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $code = null,
        public readonly ?string $channel = null,
        public readonly ?float $budget = null,
        public readonly ?string $centerId = null,
        public readonly ?\DateTimeImmutable $startDate = null,
        public readonly ?\DateTimeImmutable $endDate = null
    ) {}
}