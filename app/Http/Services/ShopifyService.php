<?php

namespace App\Http\Services;

use App\Http\Traits\ShopifyServiceHelper;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Redis;

class ShopifyService
{
    use ShopifyServiceHelper;

    /**
     * @param string|null $storeName
     * @param string|null $apiKey
     * @throws \Exception
     */
    public function __construct(?string $storeName = null, ?string $apiKey = null)
    {
        if ($this->initializeStoreDetails($storeName, $apiKey)) {
            return;
        }

        $this->initializeStoreInformation();
    }

    /**
     * @param array $parameters
     * @param bool $needPageInfo
     * @return array
     */
    public function getOrders(array $parameters = [], bool $needPageInfo = false): array
    {
        return $this->setVersion()
            ->setApiPath('orders.json')
            ->makeRequest('GET', $parameters, $needPageInfo);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getOrderCount(array $parameters = []): array
    {
        return $this->setVersion()
            ->setApiPath('orders/count.json')
            ->makeRequest('GET', $parameters);
    }

    /**
     * @param string|null $storeName
     * @param string|null $apiKey
     * @return bool
     */
    private function initializeStoreDetails(?string $storeName, ?string $apiKey): bool
    {
        if (null !== $storeName && null !== $apiKey) {
            $this->storeName     = $storeName;
            $this->shopifyApiKey = $apiKey;
            return true;
        }
        return false;
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function initializeStoreInformation(): void
    {
        $storeInfo = $this->getStoreInfo();

        if ($storeInfo) {
            $this->storeName     = $storeInfo['name'];
            $this->shopifyApiKey = $storeInfo['apiKey'];
        } else {
            throw new \Exception('Store information not found');
        }
    }

    /**
     * @return array|null
     */
    private function getStoreInfo(): ?array
    {
        $storedApiKey    = Redis::get('store:access_token');
        $storedStoreName = Redis::get('store:store_hash');

        $defaultStoreName = config('shopify_service.store_name');
        $defaultApiKey    = config('shopify_service.store_api_key');

        if (null !== $defaultStoreName && null !== $defaultApiKey) {
            return [
                'name'   => $defaultStoreName,
                'apiKey' => $defaultApiKey
            ];
        } elseif (null !== $storedStoreName && null !== $storedApiKey) {
            return [
                'name'   => $storedStoreName,
                'apiKey' => $storedApiKey
            ];
        } elseif ($storeInfo = StoreSetting::first()) {
            Redis::set('store:access_token', $storeInfo->store_api_key);
            Redis::set('store:store_hash', $storeInfo->store_name);

            return [
                'name'   => $storeInfo->store_name,
                'apiKey' => $storeInfo->store_api_key
            ];
        }

        return null;
    }
}