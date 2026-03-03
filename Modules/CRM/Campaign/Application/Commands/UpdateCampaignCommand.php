<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Commands;
class UpdateCampaignCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $code,
        public readonly ?string $channel,
        public readonly ?float $budget,
        public readonly ?string $centerId,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly bool $isActive
    ) {}
}