<?php

use App\Http\Controllers\OrderController;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->orderRepository = Mockery::mock(OrderRepository::class);
    $this->orderService = Mockery::mock(OrderService::class);

    app()->instance(OrderRepository::class, $this->orderRepository);
    app()->instance(OrderService::class, $this->orderService);

    $this->orderController = new OrderController($this->orderRepository, $this->orderService);
});

it('displays a listing of the Top Sold Products', function () {
    $topSoldProducts = collect([
        (object) ['product_name' => 'Product 1', 'total_quantity' => 100],
        (object) ['product_name' => 'Product 2', 'total_quantity' => 50],
    ]);

    $this->orderRepository
        ->shouldReceive('getTopSoldProducts')
        ->andReturn($topSoldProducts);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->component('Products/Index')
        ->has('products', 2)
        ->where('products.0.product_name', 'Product 1')
        ->where('products.0.total_quantity', 100)
        ->where('products.1.product_name', 'Product 2')
        ->where('products.1.total_quantity', 50)
        ->has('status')
        ->has('message')
    );
});

it('displays a empty listing of top products', function () {
    $this->orderRepository
        ->shouldReceive('getTopSoldProducts')
        ->andReturn(collect());

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->component('Products/Index')
        ->has('products')
        ->has('status')
        ->has('message')
    );
});

it('gets stock', function () {
    $this->orderService
        ->shouldReceive('getStock')
        ->with('MPN-12345')
        ->andReturn(100);

    $response = $this->get('/stock/get?merchant_product_no=MPN-12345');

    expect($response['stock'])->toBe(100);
});

it('adds stock', function () {
    $this->orderService
        ->shouldReceive('addStock')
        ->with('MPN-12345', 1, 50)
        ->andReturn(true);

    Session::start();
    $response = $this->put('/stock/add', [
        'merchant_product_no' => 'MPN-12345',
        'stock_location_id' => 1,
        'stock' => 50
    ]);

    $response->assertRedirect()
        ->assertSessionHas('status', 'success')
        ->assertSessionHas('message', 'Successfully added the stock(s)');
});
