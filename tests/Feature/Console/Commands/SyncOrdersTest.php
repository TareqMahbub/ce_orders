<?php

use App\Services\OrderService;
use Illuminate\Support\Facades\Artisan;

it('can calls syncOrders method', function () {
    $orderService = Mockery::mock(OrderService::class);
    $orderService->shouldReceive('syncOrders')->once();

    $this->app->instance(OrderService::class, $orderService);

    Artisan::call('orders:sync');
});

it('can display success message', function () {
    $orderService = Mockery::mock(OrderService::class);
    $orderService->shouldReceive('syncOrders')->once();

    $this->app->instance(OrderService::class, $orderService);

    $this->artisan('orders:sync')
        ->expectsOutput('Orders synced with CE.')
        ->assertExitCode(0);
});

it('can display error message on exception', function () {
    $orderService = Mockery::mock(OrderService::class);
    $orderService->shouldReceive('syncOrders')->andThrow(new Exception('Test exception'));

    $this->app->instance(OrderService::class, $orderService);

    $this->artisan('orders:sync')
        ->expectsOutput('Syncing failed. Exception message: Test exception')
        ->assertExitCode(0);
});
