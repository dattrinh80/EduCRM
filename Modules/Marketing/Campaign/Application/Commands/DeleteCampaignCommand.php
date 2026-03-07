<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
class DeleteCampaignCommand
{
    public function __construct(public readonly string $id) {}
}