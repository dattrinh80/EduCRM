<?php

declare(strict_types=1);

namespace Modules\Core\User\Domain\Enums;

enum ScopeType: string
{
    case SYSTEM = 'SYSTEM';
    case REGION = 'REGION';
    case CENTER = 'CENTER';

    public function label(): string
    {
        return match($this) {
            self::SYSTEM => 'Hệ thống (Toàn bộ cơ sở)',
            self::REGION => 'Khu vực',
            self::CENTER => 'Cơ sở',
        };
    }
}
