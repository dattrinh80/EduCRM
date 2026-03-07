<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\CRM\Lead\Application\Commands\CreateLeadCommand;
use Modules\CRM\Lead\Application\Commands\CreateLeadHandler;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;
use Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Modules\Marketing\InterestType\Infrastructure\ReadModels\InterestTypeReadModel;

class LeadsImport implements ToCollection, WithHeadingRow
{
    private CreateLeadHandler $handler;

    public function __construct(CreateLeadHandler $handler)
    {
        $this->handler = $handler;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validate basic required fields from row
            if (!isset($row['name']) || !isset($row['phone']) || !isset($row['center_code'])) {
                continue; // Skip invalid row or log error
            }

            // Map codes to IDs
            $centerId = null;
            if (!empty($row['center_code'])) {
                $center = CenterReadModel::where('code', $row['center_code'])->first();
                if ($center) {
                    $centerId = $center->id;
                } else {
                    continue; // Skip if mandatory center is not found
                }
            }

            $leadSourceId = null;
            if (!empty($row['lead_source_code'])) {
                $leadSource = LeadSourceReadModel::where('code', $row['lead_source_code'])->first();
                if ($leadSource) $leadSourceId = $leadSource->id;
            }

            $campaignId = null;
            if (!empty($row['campaign_code'])) {
                $campaign = CampaignReadModel::where('code', $row['campaign_code'])->first();
                if ($campaign) $campaignId = $campaign->id;
            }

            $interestTypeId = null;
            if (!empty($row['interest_type_code'])) {
                $interestType = InterestTypeReadModel::where('code', $row['interest_type_code'])->first();
                if ($interestType) $interestTypeId = $interestType->id;
            }

            $dob = null;
            if (!empty($row['dob'])) {
                // simple assume Y-m-d format
                $dob = $row['dob'];
                // optionally format dates if it comes from excel numbers
                if (is_numeric($dob)) {
                    $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d');
                }
            }

            $command = new CreateLeadCommand(
                (string)$row['name'],
                (string)$row['phone'],
                empty($row['email']) ? null : (string)$row['email'],
                $centerId,
                $dob,
                $leadSourceId,
                $campaignId,
                $interestTypeId,
                null // assigned_to is left null
            );

            try {
                $this->handler->handle($command);
            } catch (\Exception $e) {
                // Log the duplicate or the error
                \Illuminate\Support\Facades\Log::error('Lead Import Error: ' . $e->getMessage(), ['row' => $row]);
            }
        }
    }
}
