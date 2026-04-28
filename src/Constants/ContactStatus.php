<?php

namespace App\Constants;

enum ReservationStatus: string
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case NO_SHOW   = 'no_show';

    public function isResolved(): bool
    {
        return match($this) {
            self::CANCELLED, self::COMPLETED, self::NO_SHOW => true,
            default => false,
        };
    }
}