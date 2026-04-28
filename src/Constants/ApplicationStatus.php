<?php

namespace App\Constants;

enum ApplicationStatus: string
{
    case PENDING     = 'pending';
    case REVIEWED    = 'reviewed';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEWED = 'interviewed';
    case ACCEPTED    = 'accepted';
    case REJECTED    = 'rejected';

    public function isClosed(): bool
    {
        return match($this) {
            self::ACCEPTED, self::REJECTED => true,
            default                        => false,
        };
    }

    public function canTransitionTo(self $next): bool
    {
        $allowed = match($this) {
            self::PENDING     => [self::REVIEWED, self::REJECTED],
            self::REVIEWED    => [self::SHORTLISTED, self::REJECTED],
            self::SHORTLISTED => [self::INTERVIEWED, self::REJECTED],
            self::INTERVIEWED => [self::ACCEPTED, self::REJECTED],
            default           => [],
        };
        return in_array($next, $allowed, true);
    }
}