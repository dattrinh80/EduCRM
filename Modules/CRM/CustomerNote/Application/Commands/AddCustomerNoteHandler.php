<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\CustomerNote\Domain\CustomerNote;
use Modules\CRM\CustomerNote\Domain\CustomerNoteRepositoryInterface;
use Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityCommand;
use Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityHandler;
use Modules\CRM\CustomerActivity\Domain\CustomerActivity;

class AddCustomerNoteHandler
{
    public function __construct(
        private CustomerNoteRepositoryInterface $noteRepository,
        private AddCustomerActivityHandler $activityHandler
    ) {}

    public function handle(AddCustomerNoteCommand $command): CustomerNote
    {
        $note = CustomerNote::create(
            (string) Str::uuid(),
            $command->customerId,
            $command->content,
            $command->createdBy
        );

        $this->noteRepository->save($note);

        // Auto-log activity for note creation
        $this->activityHandler->handle(new AddCustomerActivityCommand(
            $command->customerId,
            CustomerActivity::TYPE_NOTE,
            'Đã thêm ghi chú mới',
            $command->createdBy
        ));

        return $note;
    }
}

