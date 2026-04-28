<?php

namespace App\Constants;

enum OrderStatus: string
{
    case PENDING          = 'pending';
    case CONFIRMED        = 'confirmed';
    case PREPARING        = 'preparing';
    case READY            = 'ready';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED        = 'delivered';
    case CANCELLED        = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING          => 'Pending',
            self::CONFIRMED        => 'Confirmed',
            self::PREPARING        => 'Preparing',
            self::READY            => 'Ready for Delivery',
            self::OUT_FOR_DELIVERY => 'Out for Delivery',
            self::DELIVERED        => 'Delivered',
            self::CANCELLED        => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING          => 'badge-warning',
            self::CONFIRMED, self::PREPARING => 'badge-info',
            self::READY            => 'badge-success',
            self::OUT_FOR_DELIVERY => 'badge-primary',
            self::DELIVERED        => 'badge-success',
            self::CANCELLED        => 'badge-error',
        };
    }

    /**
     * Checks if the order has reached a final state.
     * Returns true if the order is 'Delivered' or 'Cancelled'.
     * Returns false for all other statuses, indicating the order is still active.
     * This method can be used to determine if an order can be modified or if it should be treated as completed.
     */
    public function isClosed(): bool
    {
        return match($this) {
            self::DELIVERED, self::CANCELLED => true,
            default                          => false,
        };
    }

    public function isActive(): bool
    {
        return !$this->isClosed();
    }

    /* Returns the corresponding database ID for this status.
     * This method performs a lookup in the `order_status` table to find the ID associated with the enum's string value.
     * It caches the mapping of status names to IDs on first access for performance.
     * Throws a RuntimeException if the status name is not found in the database, which should never happen if the database is properly seeded.
     */
    public function id(): int
    {
        return self::resolveIds()[$this->value]
            ?? throw new \RuntimeException("OrderStatus '{$this->value}' not found in database.");
    }

    /* Caches and returns a mapping of status names to their corresponding database IDs.
     * This method queries the `order_status` table once and stores the results in a static variable for subsequent calls.
     * The returned array maps status_name (string) to id (int), allowing for efficient lookups when calling the `id()` method on the enum.
     */
    private static function resolveIds(): array
    {
        static $map = null;
        if ($map === null) {
            $rows = \App\Core\Database::getInstance()->fetchAll("SELECT id, status_name FROM order_status");
            $map = array_column($rows, 'id', 'status_name');
        }
        return $map;
    }
}