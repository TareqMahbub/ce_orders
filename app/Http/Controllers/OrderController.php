<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetStockRequest;
use App\Http\Requests\SetStockRequest;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\ResponseFactory;

class OrderController extends Controller
{
    private OrderRepository $orderRepository;
    private OrderService $orderService;

    public function __construct(OrderRepository $orderRepository, OrderService $orderService)
    {
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    public function index(): Response|ResponseFactory
    {
        return inertia('Products/Index', [
            'products' => $this->orderRepository->getTopSoldProducts(),
            'status' => request()->session()->get('status'),
            'message' => request()->session()->get('message'),
        ]);
    }

    public function getStock(GetStockRequest $request): array
    {
        $inputs = $request->validated();

        return [
            'stock' => $this->orderService->getStock($inputs['merchant_product_no']),
        ];
    }

    public function addStock(SetStockRequest $request): RedirectResponse
    {
        $inputs = $request->validated();

        $isSuccess = $this->orderService->addStock($inputs['merchant_product_no'], $inputs['stock_location_id'], $inputs['stock']);

        return redirect()
            ->back()
            ->with('status', $isSuccess ? 'success' : 'error')
            ->with('message', $isSuccess ? 'Successfully added the stock(s)' : 'Failed to add the stock(s)');
    }
}
