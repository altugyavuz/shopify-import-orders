<?php

namespace App\Http\Traits;

use App\Http\Services\ShopifyService;
use Illuminate\Support\Facades\Redis;

trait SetupHelper
{

    private string $storeSetupKey = "welcome:store:setup";
    /**
     * @param string $storeName
     * @param string $accessToken
     * @return bool
     */
    private function checkStoreInformation(string $storeName, string $accessToken): bool
    {
        try {
            $service = new ShopifyService($storeName, $accessToken);
            $response = $service->getOrders(['limit' => 1]);
        } catch (\Exception $e) {
            report($e);
            return false;
        }

        return $response['success'];
    }

    /**
     * @return null|string
     */
    public function checkSetupIsDone(): ?string
    {
        return Redis::get($this->storeSetupKey);
    }

    /**
     * @return void
     */
    public function markSetupIsDone(): void
    {
        Redis::set($this->storeSetupKey, 1);
    }
}
