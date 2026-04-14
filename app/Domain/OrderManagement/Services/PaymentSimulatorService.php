<?php

namespace App\Domain\OrderManagement\Services;

class PaymentSimulatorService
{
    public function process(float $total, string $paymentMethod): bool
    {
        if ($total > 999) {
            return false;
        }

        return true;
    }
}
