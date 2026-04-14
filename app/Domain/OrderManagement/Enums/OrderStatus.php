<?php

namespace App\Domain\OrderManagement\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function canTransitionTo(self $next): bool
    {
        return match($this) {
            self::Pending   => $next === self::Paid,
            self::Paid      => $next === self::Shipped,
            self::Shipped   => $next === self::Delivered,
            self::Delivered => false,
        };
    }
}
