<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Domain;

use App\Core\Domain\Entity;

class LeadActivity extends Entity
{
    public const TYPE_CALL = 'call';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_SMS = 'sms';
    public const TYPE_EMAIL = 'email';
    public const TYPE_NOTE = 'note';
    public const TYPE_STATUS_CHANGE = 'status_change';
    public const TYPE_ASSIGNMENT = 'assignment';
    public const TYPE_CONVERSION = 'conversion';

    public const VALID_TYPES = [
        self::TYPE_CALL,
        self::TYPE_MEETING,
        self::TYPE_SMS,
        self::TYPE_EMAIL,
        self::TYPE_NOTE,
        self::TYPE_STATUS_CHANGE,
        self::TYPE_ASSIGNMENT,
        self::TYPE_CONVERSION,
    ];

    public function __construct(
        string $id,
        public string $leadId,
        public string $activityType,
        public ?string $description,
        public ?string $createdBy,
        public ?\DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;

        if (!in_array($activityType, self::VALID_TYPES, true)) {
            throw new \InvalidArgumentException("Invalid activity type: {$activityType}");
        }
    }

    public static function create(
        string $id,
        string $leadId,
        string $activityType,
        ?string $description = null,
        ?string $createdBy = null
    ): self {
        return new self(
            $id,
            $leadId,
            $activityType,
            $description,
            $createdBy,
            new \DateTimeImmutable()
        );
    }
}
