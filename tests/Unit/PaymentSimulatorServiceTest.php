<?php

use App\Domain\OrderManagement\Services\PaymentSimulatorService;

test('payment succeeds for orders under 999', function () {
    $service = new PaymentSimulatorService();
    expect($service->process(500.00, 'credit_card'))->toBeTrue();
});

test('payment succeeds for order exactly at 999', function () {
    $service = new PaymentSimulatorService();
    expect($service->process(999.00, 'credit_card'))->toBeTrue();
});

test('payment fails for orders over 999', function () {
    $service = new PaymentSimulatorService();
    expect($service->process(1000.00, 'credit_card'))->toBeFalse();
});
