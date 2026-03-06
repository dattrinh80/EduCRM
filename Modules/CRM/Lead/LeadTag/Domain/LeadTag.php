<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadTag\Domain;

use App\Core\Domain\Entity;

class LeadTag extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public ?string $color = 'slate'
    ) {
        $this->id = $id;
    }

    public static function create(string $id, string $name, ?string $color = 'slate'): self
    {
        return new self($id, $name, $color);
    }
}
