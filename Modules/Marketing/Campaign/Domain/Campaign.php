<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Domain;

use App\Core\Domain\Entity;

class Campaign extends Entity
{
    public string $name;
    public ?string $code;
    public ?string $channel;
    public ?float $budget;
    public ?string $centerId;
    public ?\DateTimeImmutable $startDate;
    public ?\DateTimeImmutable $endDate;
    public bool $isActive;
    public ?\DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $name,
        ?string $code,
        ?string $channel,
        ?float $budget,
        ?string $centerId,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        bool $isActive,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->name = $name;
        $this->code = $code;
        $this->channel = $channel;
        $this->budget = $budget;
        $this->centerId = $centerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isActive = $isActive;
    }

    public static function create(
        string $name,
        ?string $code,
        ?string $channel,
        ?float $budget,
        ?string $centerId,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate
    ): self {
        return new self(
            (string) \Illuminate\Support\Str::uuid(),
            $name,
            $code,
            $channel,
            $budget,
            $centerId,
            $startDate,
            $endDate,
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        ?string $code,
        ?string $channel,
        ?float $budget,
        ?string $centerId,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        bool $isActive
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->channel = $channel;
        $this->budget = $budget;
        $this->centerId = $centerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
    }
}