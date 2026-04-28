<?php

namespace App\Constants;

enum PaymentMethod: string
{
    case CASH   = 'cash';
    case PAYPAL = 'paypal';

    public function isOnline(): bool
    {
        return $this === self::PAYPAL;
    }
}