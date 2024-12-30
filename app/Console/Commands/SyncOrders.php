<?php

namespace App\Console\Commands;

use App\Services\CeService;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Exception;

class SyncOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync orders';

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle(): void
    {
        try {
            $this->orderService->syncOrders();
            $this->info('Orders synced with CE.');
        } catch(Exception $e) {
            $this->error("Syncing failed. Exception message: {$e->getMessage()}");
        }
    }
}
