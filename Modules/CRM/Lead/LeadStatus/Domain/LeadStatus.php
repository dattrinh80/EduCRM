<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadStatus\Domain;

use App\Core\Domain\Entity;

class LeadStatus extends Entity
{
    public const STAGE_NEW = 'NEW';
    public const STAGE_CONTACTED = 'CONTACTED';
    public const STAGE_INTERESTED = 'INTERESTED';
    public const STAGE_QUALIFIED = 'QUALIFIED';
    public const STAGE_CONVERTED = 'CONVERTED';
    public const STAGE_LOST = 'LOST';

    public function __construct(
        string $id,
        public string $name,
        public string $stage,
        public int $sortOrder = 0,
        public bool $isActive = true,
        public ?string $color = null
    ) {
        $this->id = $id;

        if (!in_array($stage, [
            self::STAGE_NEW,
            self::STAGE_CONTACTED,
            self::STAGE_INTERESTED,
            self::STAGE_QUALIFIED,
            self::STAGE_CONVERTED,
            self::STAGE_LOST,
        ], true)) {
            throw new \InvalidArgumentException("Invalid stage: {$stage}");
        }
    }

    public static function create(
        string $id,
        string $name,
        string $stage,
        int $sortOrder = 0,
        bool $isActive = true,
        ?string $color = null
    ): self {
        return new self($id, $name, $stage, $sortOrder, $isActive, $color);
    }
}
