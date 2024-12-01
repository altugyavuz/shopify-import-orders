<?php

namespace App\Jobs;

use App\Http\Services\ShopifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use phpDocumentor\Reflection\Types\Self_;

class PrepareOrderImportJob implements ShouldQueue
{
    use Queueable;

    private const PAGE_SIZE = 10;
    private const STATUS    = "any";

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int     $page = 1,
        private int     $maxPage = 0,
        private ?string $pageInfo = null,
        private ?string $createdAtMin = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ShopifyService();

        if ($this->page == 1) {
            $orderCount = $service->getOrderCount([
                'status' => self::STATUS,
            ]);

            if ($orderCount['success']) {
                $this->maxPage = $this->calculateTotalPages($orderCount['count']);
            }
        }

        if ($this->maxPage) {
            list($startPage, $lastPage) = $this->calculatePageRange();

            $this->prepareImportJobs($service, $startPage, $lastPage);

            if ($lastPage < $this->maxPage) {
                dispatch(new PrepareOrderImportJob(
                    $lastPage,
                    $this->maxPage,
                    $this->pageInfo,
                    $this->createdAtMin
                ));
            }

        } else {
            $this->fail('No orders found or error occurred when calculating total pages.');
        }
    }

    /**
     * @param \App\Http\Services\ShopifyService $service
     * @param int $startPage
     * @param int $lastPage
     * @return void
     */
    private function prepareImportJobs(ShopifyService $service, int $startPage, int $lastPage): void
    {
        $this->createdAtMin = now()->subYearWithoutOverflow()->toIso8601String();

        $parameters = [
            'created_at_min' => $this->createdAtMin,
            'limit'          => self::PAGE_SIZE,
            'status'         => self::STATUS,
            'fields'         => 'id',
        ];

        $attempt     = 0;
        $maxAttempts = 3;
        for ($i = $startPage; $i <= $lastPage; $i++) {
            if (null !== $this->pageInfo) {
                $parameters = [
                    'page_info' => $this->pageInfo,
                    'fields'    => 'id'
                ];
            }

            if ($i > 1 && null === $this->pageInfo) {
                break;
            }

            try {
                $orderData = $service->getOrders($parameters, true);
            } catch (\Exception $e) {
                if ($attempt++ < $maxAttempts) {
                    $i--;
                }

                sleep(5);
                report($e);
                continue;
            }

            if ($orderData['success'] && !empty($orderData['orders'])) {
                // Dispatch import job
                dispatch(new ImportShopifyOrderJob($this->pageInfo, $this->createdAtMin));

                if ($orderData['next']) {
                    $this->pageInfo = $orderData['next'];
                    $attempt        = 0;
                } else {
                    break;
                }
            } elseif ($orderData['statusCode'] == 429) {
                sleep(5);
                if ($attempt++ < $maxAttempts) {
                    $i--;
                }
            } else {
                break;
            }
        }
    }

    /**
     * @param int $productCount
     * @return int
     */
    private function calculateTotalPages(int $productCount): int
    {
        $totalPages = ceil($productCount / self::PAGE_SIZE);

        if ($totalPages % self::PAGE_SIZE != 0) {
            $totalPages = (int)ceil($totalPages / self::PAGE_SIZE) * self::PAGE_SIZE;
        }

        return $totalPages;
    }

    /**
     * @return int[]
     */
    private function calculatePageRange(): array
    {
        $startPage = $this->isCurrentPageFirst() ? 1 : $this->page + 1;

        if ($this->page % self::PAGE_SIZE != 0) {
            $increment = self::PAGE_SIZE - ($this->page % self::PAGE_SIZE);
        } else {
            $increment = self::PAGE_SIZE;
        }
        $lastPage = $this->isCurrentPageFirst() ? self::PAGE_SIZE : $this->page + $increment;

        $this->page = $lastPage;

        return [$startPage, $lastPage];
    }

    /**
     * @return bool
     */
    private function isCurrentPageFirst(): bool
    {
        return $this->page == 1;
    }
}
