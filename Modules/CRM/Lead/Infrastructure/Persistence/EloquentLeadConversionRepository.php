<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\Persistence;

use Modules\CRM\Lead\Domain\LeadConversion;
use Modules\CRM\Lead\Domain\LeadConversionRepositoryInterface;

class EloquentLeadConversionRepository implements LeadConversionRepositoryInterface
{
    public function save(LeadConversion $conversion): void
    {
        LeadConversionModel::updateOrCreate(
            ['id' => $conversion->id],
            [
                'lead_id' => $conversion->leadId,
                'student_id' => $conversion->studentId,
                'converted_by' => $conversion->convertedBy,
                'converted_at' => $conversion->convertedAt ?? now(),
            ]
        );
    }

    public function findByLeadId(string $leadId): array
    {
        $models = LeadConversionModel::where('lead_id', $leadId)->get();
        
        return $models->map(function ($model) {
            return new LeadConversion(
                id: $model->id,
                leadId: $model->lead_id,
                studentId: $model->student_id,
                convertedBy: $model->converted_by,
                convertedAt: $model->converted_at?->toDateTimeString()
            );
        })->toArray();
    }
}
