<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\Source\Domain\Source;
use Modules\CRM\Source\Domain\SourceRepositoryInterface;

class CreateSourceHandler
{
    public function __construct(
        private SourceRepositoryInterface $repository
    ) {}

    public function handle(CreateSourceCommand $command): string
    {
        $id = Str::uuid()->toString();

        $source = Source::create(
            $id,
            $command->name,
            $command->code
        );

        $this->repository->save($source);

        return $id;
    }
}
