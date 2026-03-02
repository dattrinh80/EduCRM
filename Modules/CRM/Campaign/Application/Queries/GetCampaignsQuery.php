<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Queries;
class GetCampaignsQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?bool $isActive = null
    ) {}
}