<?php

namespace App\Http\Traits;

use App\Http\Services\ShopifyService;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Redis;

trait SetupHelper
{

    private string $storeSetupKey = "welcome:store:setup";
    private string $storeSetupCheckKey = "welcome:store:setup_check";
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
        if (Redis::get($this->storeSetupCheckKey) && Redis::get($this->storeSetupKey)) {
            return "1";
        } else {
            $configCheck = null !== config('shopify_service.store_api_key') && null !== config('shopify_service.store_api_key');
            $storeInfo = StoreSetting::first();
            $storeCheck = $storeInfo && $storeInfo->store_name && $storeInfo->store_api_key;

            if ($configCheck || $storeCheck) {
                Redis::set($this->storeSetupCheckKey, 1, 60 * 60 * 3);

                return "1";
            } else {
                return null;
            }
        }
    }

    /**
     * @return void
     */
    public function markSetupIsDone(): void
    {
        Redis::set($this->storeSetupKey, 1, 60 * 60 * 24 * 5);
    }
}
