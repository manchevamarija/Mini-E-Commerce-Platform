<?php

namespace App\Domain\OrderManagement\DTOs;

use App\Domain\OrderManagement\Enums\PaymentMethod;

readonly class CreateOrderDTO
{
    public function __construct(
        public int $userId,
        public PaymentMethod $paymentMethod,
        public float $total,
    ) {}
}
