<?php

namespace App\Constants;

enum OrderType: string
{
    case DINE_IN  = 'dine_in';
    case DELIVERY = 'delivery';
    case TAKEOUT  = 'takeout';

    public function label(): string
    {
        return match($this) {
            self::DINE_IN  => 'Dine In',
            self::DELIVERY => 'Delivery',
            self::TAKEOUT  => 'Takeout',
        };
    }

    public function requiresAddress(): bool
    {
        return $this === self::DELIVERY;
    }

    public function hasDeliveryFee(): bool
    {
        return $this === self::DELIVERY;
    }
}