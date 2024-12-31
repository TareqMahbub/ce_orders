<?php

use App\Services\CeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->ceService = new CeService();
});

it('should set stock successfully', function () {
    Http::fake([
        '*' => Http::response(['StatusCode' => 200, 'Success' => true])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeTrue();
});

it('should fail with bad StatusCode', function () {
    Http::fake([
        '*' => Http::response(['StatusCode' => 400, 'Success' => true])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeFalse();
});

it('should fail when Success = false', function () {
    Http::fake([
        '*' => Http::response(['StatusCode' => 200, 'Success' => false])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeFalse();
});

it('should fail without StatusCode', function () {
    Http::fake([
        '*' => Http::response(['Success' => true])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeFalse();
});

it('should fail without Success', function () {
    Http::fake([
        '*' => Http::response(['StatusCode' => 200])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeFalse();
});
