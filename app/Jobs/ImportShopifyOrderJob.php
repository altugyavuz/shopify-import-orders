<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Http\Services\ShopifyService;
use App\Models\ShopifyOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class ImportShopifyOrderJob implements ShouldQueue
{
    use Queueable;

    private const PAGE_SIZE    = 10;
    private const ORDER_STATUS = "any";
    private const MAX_ATTEMPTS = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly ?string $pageInfo = null,
        private readonly ?string $createdAtMin = null,
        private readonly int     $attempts = 0
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->attempts > self::MAX_ATTEMPTS) {
            return;
        }

        if (null !== $this->pageInfo) {
            $parameters = [
                'limit'     => self::PAGE_SIZE,
                'page_info' => $this->pageInfo,
            ];
        } else {
            $parameters = [
                'status' => self::ORDER_STATUS,
                'limit'  => self::PAGE_SIZE,
            ];

            if (null !== $this->createdAtMin) {
                $parameters['created_at_min'] = $this->createdAtMin;
            }
        }

        $service = new ShopifyService();
        try {
            $orders = $service->getOrders($parameters, true);
        } catch (\Exception $e) {
            self::dispatch($this->pageInfo, $this->createdAtMin, $this->attempts + 1)
                ->delay(now()->addSeconds(10));

            report($e);
            return;
        }

        if ($orders['success'] && !empty($orders['orders'])) {
            $orderData = [];

            foreach ($orders['orders'] as $order) {
                $orderData[] = [
                    'shopify_order_id' => $order['id'],
                    'customer_email'   => $order['email'],
                    'total_price'      => $order['total_price'],
                    'status'           => $this->getOrderStatus($order),
                    'ordered_at'       => Carbon::parse($order['created_at'])->format('Y-m-d H:i:s'),
                    'created_at'       => now()->format('Y-m-d H:i:s'),
                    'updated_at'       => now()->format('Y-m-d H:i:s')
                ];
            }

            ShopifyOrder::insert($orderData);

            if (null === $orders['next']) {
                Redis::set('shopify_orders_importing', 0);
            }
        } elseif ($orders['statusCode'] == 429) {
            self::dispatch($this->pageInfo, $this->createdAtMin, $this->attempts + 1)
                ->delay(now()->addSeconds(10));
        } else {
            $this->fail(new \Exception('Failed to import orders from Shopify.', $orders['statusCode'], $orders));
        }
    }

    /**
     * @param array $order
     * @return \App\Enums\OrderStatus
     */
    function getOrderStatus(array $order): OrderStatus
    {
        return match (true) {
            $order['closed_at'] !== null    => OrderStatus::Closed,
            $order['cancelled_at'] !== null => OrderStatus::Cancelled,
            default                         => OrderStatus::Open,
        };
    }

}
