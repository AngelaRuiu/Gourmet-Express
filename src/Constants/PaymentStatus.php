<?php

namespace App\Constants;

enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case PAID     = 'paid';
    case FAILED   = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING  => 'Awaiting Payment',
            self::PAID     => 'Paid',
            self::FAILED   => 'Payment Failed',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Checks if the payment has reached a final state.
     * Returns true if the payment is 'Paid' or 'Refunded'.
     * Returns false for 'Pending' and 'Failed', indicating the payment is still active or needs attention.
     * This method can be used to determine if a payment can be modified or if it should be treated as completed.
     */
    public function isResolved(): bool
    {
        return match($this) {
            self::PAID, self::REFUNDED => true,
            default                    => false,
        };
    }

    /* Returns the corresponding database ID for this status.
     * This method performs a lookup in the `payment_status` table to find the ID associated with the enum's string value.
     * It caches the mapping of status names to IDs on first access for performance.
     * Throws a RuntimeException if the status name is not found in the database, which should never happen if the database is properly seeded.
     */
    public function id(): int
    {
        return self::resolveIds()[$this->value]
            ?? throw new \RuntimeException("PaymentStatus '{$this->value}' not found in database.");
    }

    /* Cache the mapping of status_name → id from the database to avoid repeated queries. */
    private static function resolveIds(): array
    {
        static $map = null;
        if ($map === null) {
            $rows = \App\Core\Database::getInstance()->fetchAll("SELECT id, status_name FROM payment_status");
            $map = array_column($rows, 'id', 'status_name');
        }
        return $map;
    }
}