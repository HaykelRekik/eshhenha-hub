<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SupportTicketStatus: string implements HasColor, HasIcon, HasLabel
{
    case NEW = 'new';
    case IN_PROGRESS = 'in progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::NEW => PhosphorIcons::SparkleDuotone,
            self::IN_PROGRESS => PhosphorIcons::ClockCountdownDuotone,
            self::RESOLVED => PhosphorIcons::CheckCircleDuotone,
            self::CLOSED => PhosphorIcons::XCircleDuotone,
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NEW => 'info',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
            self::CLOSED => 'danger',
        };
    }
}
