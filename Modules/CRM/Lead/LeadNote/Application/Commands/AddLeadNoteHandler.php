<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadNote\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\Lead\LeadNote\Domain\LeadNote;
use Modules\CRM\Lead\LeadNote\Domain\LeadNoteRepositoryInterface;
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityCommand;
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityHandler;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivity;

class AddLeadNoteHandler
{
    public function __construct(
        private LeadNoteRepositoryInterface $noteRepository,
        private AddLeadActivityHandler $activityHandler
    ) {}

    public function handle(AddLeadNoteCommand $command): LeadNote
    {
        $note = LeadNote::create(
            (string) Str::uuid(),
            $command->leadId,
            $command->content,
            $command->createdBy
        );

        $this->noteRepository->save($note);

        // Auto-log activity for note creation
        $this->activityHandler->handle(new AddLeadActivityCommand(
            $command->leadId,
            LeadActivity::TYPE_NOTE,
            'Đã thêm ghi chú mới',
            $command->createdBy
        ));

        return $note;
    }
}
