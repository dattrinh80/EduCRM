<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;
use Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Modules\Marketing\InterestType\Infrastructure\ReadModels\InterestTypeReadModel;
use Modules\CRM\Lead\Domain\Lead;

class ImportLeadHandler
{
    private CreateLeadHandler $createLeadHandler;

    public function __construct(CreateLeadHandler $createLeadHandler)
    {
        $this->createLeadHandler = $createLeadHandler;
    }

    public function handle(ImportLeadCommand $command): Lead
    {
        if (empty($command->name) || empty($command->phone)) {
            throw new \InvalidArgumentException("Thiếu cột bắt buộc (name, phone)");
        }

        $centerId = null;
        if (!empty($command->centerCode)) {
            $center = CenterReadModel::where('code', $command->centerCode)->first();
            if (!$center) {
                throw new \InvalidArgumentException("Mã cơ sở ({$command->centerCode}) không tồn tại.");
            }
            $centerId = $center->id;
        }

        $leadSourceId = null;
        if (!empty($command->leadSourceCode)) {
            $leadSource = LeadSourceReadModel::where('code', $command->leadSourceCode)->first();
            if ($leadSource) {
                $leadSourceId = $leadSource->id;
            }
        }

        $campaignId = null;
        if (!empty($command->campaignCode)) {
            $campaign = CampaignReadModel::where('code', $command->campaignCode)->first();
            if ($campaign) {
                $campaignId = $campaign->id;
            }
        }

        $interestTypeId = null;
        if (!empty($command->interestTypeCode)) {
            $interestType = InterestTypeReadModel::where('code', $command->interestTypeCode)->first();
            if ($interestType) {
                $interestTypeId = $interestType->id;
            }
        }

        $dob = null;
        if (!empty($command->dob)) {
            $dobStr = (string)$command->dob;
            if (strtotime($dobStr)) {
                $dob = date('Y-m-d', strtotime($dobStr));
            } elseif (is_numeric($dobStr)) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dobStr)->format('Y-m-d');
            }
        }

        $createCommand = new CreateLeadCommand(
            $command->name,
            $command->phone,
            $command->email,
            $centerId,
            $dob,
            $leadSourceId,
            $campaignId,
            $interestTypeId,
            null
        );

        return $this->createLeadHandler->handle($createCommand);
    }
}
