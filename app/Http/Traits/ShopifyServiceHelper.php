<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;

trait ShopifyServiceHelper
{
    /**
     * Shopify API Base URL
     *
     * @var string
     */
    protected string $baseUrl = 'https://%s.myshopify.com/admin/api/%s/';
    /**
     * Shopify API Version
     *
     * @var string
     */
    protected string $apiVersion = '2024-07';
    /**
     * Shopify API Key
     *
     * @var string
     */
    protected string $shopifyApiKey = '';
    /**
     * Shopify unique store URL
     *
     * @var string
     */
    protected string $storeName = '';
    /**
     * API request URL Path
     *
     * @var string
     */
    protected string $apiPath = '';
    /**
     * Full Request URL
     *
     * @var string
     */
    protected string $requestUrl = '';
    /**
     * For individual headers
     *
     * @var array
     */
    protected array $headers = [];
    /**
     * The HTTP client or HTTP-related service.
     *
     * @var mixed
     */
    protected $http;

    /**
     * @param string $storeName
     * @return \App\Http\Services\ShopifyService|\App\Http\Traits\ShopifyServiceHelper
     */
    protected function setStoreName(string $storeName): self
    {
        $this->storeName = $storeName;

        return $this;
    }

    /**
     * @param string $version
     * @return \App\Http\Services\ShopifyService|\App\Http\Traits\ShopifyServiceHelper
     */
    protected function setVersion(string $version = "2024-07"): self
    {
        if ($version != "2024-07" && $version != "2024-04") {
            $version = "2024-07";
        }

        $this->apiVersion = $version;

        return $this;
    }

    /**
     * @param string $apiKey
     * @return \App\Http\Services\ShopifyService|\App\Http\Traits\ShopifyServiceHelper
     */
    protected function setAccessToken(string $apiKey): self
    {
        $this->shopifyApiKey = $apiKey;

        return $this;
    }

    /**
     * @param array $headers
     * @return \App\Http\Services\ShopifyService|\App\Http\Traits\ShopifyServiceHelper
     */
    protected function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $path
     * @return \App\Http\Services\ShopifyService|\App\Http\Traits\ShopifyServiceHelper
     */
    protected function setApiPath(string $path): self
    {
        if ($path[0] == '/') {
            $path = substr($path, 1);
        }

        $this->apiPath = $path;

        return $this;
    }

    /**
     * @return void
     */
    protected function setRequestUrl(): void
    {
        $this->baseUrl    = sprintf($this->baseUrl, $this->storeName, $this->apiVersion);
        $this->requestUrl = $this->baseUrl . $this->apiPath;
    }

    /**
     * Create Client with Headers
     *
     * @return void
     */
    protected function createClient(): void
    {
        $data = [];

        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $header) {
                $data[$key] = $header;
            }
        }

        if ($this->shopifyApiKey != "") {
            $data['X-Shopify-Access-Token'] = $this->shopifyApiKey;
        }

        if (!isset($data['Accept'])) {
            $data['Accept'] = 'application/json';
        }

        if (!isset($data['Content-Type'])) {
            $data['Content-Type'] = 'application/json';
        }

        $this->setRequestUrl();

        $this->http = Http::withHeaders($data);
    }

    /**
     * @param string $type
     * @param mixed|array $parameters
     * @param bool $needPageInfo
     * @return array
     */
    public function makeRequest(string $type, array $parameters = [], bool $needPageInfo = false): array
    {
        // Create new HTTP client with headers (if exists)
        $this->createClient();

        return match (strtolower($type)) {
            'post'   => $this->postRequest($parameters),
            'put'    => $this->putRequest($parameters),
            'delete' => $this->deleteRequest($parameters),
            default  => $this->getRequest($parameters, $needPageInfo),
        };
    }

    /**
     * @param mixed $parameters
     * @param bool $needPageInfo
     * @return array
     */
    protected function getRequest(mixed $parameters, bool $needPageInfo = false): array
    {
        if (!is_array($parameters)) {
            $parameters = (array)$parameters;
        }

        $response = $this->http->timeout(10)->withoutVerifying()->get($this->requestUrl, $parameters);

        if ($response->successful()) {
            $pageInfo     = ['next' => null, 'previous' => null];
            $headers      = ['headers' => $response->headers()];
            $responseData = is_array($response->json()) ? $response->json() : [];

            if ($needPageInfo) {
                $pageInfo = $this->extractPageInfo($response->headers());
            }

            return array_merge(
                $responseData,
                $pageInfo,
                $headers,
                ['success' => true, 'statusCode' => $response->status()]
            );
        } else {
            return [
                'success'    => false,
                'data'       => $response->json(),
                'error_bag'  => $response->body(),
                'method'     => 'GET',
                'url'        => $this->requestUrl,
                'statusCode' => $response->status(),
                'headers'    => $response->headers()
            ];
        }
    }

    /**
     * @param mixed $parameters
     * @return array
     */
    protected function postRequest(mixed $parameters): array
    {
        if (!is_array($parameters)) {
            $parameters = (array)$parameters;
        }

        $response = $this->http->post($this->requestUrl, $parameters);

        if ($response->successful()) {
            $headers      = ['headers' => $response->headers()];
            $responseData = is_array($response->json()) ? $response->json() : [];

            return array_merge($responseData, $headers, ['success' => true, 'statusCode' => $response->status()]);
        } else {
            return [
                'success'    => false,
                'data'       => $response->json(),
                'error_bag'  => $response->body(),
                'method'     => 'POST',
                'url'        => $this->requestUrl,
                'statusCode' => $response->status(),
                'headers'    => $response->headers()
            ];
        }
    }

    /**
     * @param mixed $parameters
     * @return array
     */
    protected function putRequest(mixed $parameters): array
    {
        if (!is_array($parameters)) {
            $parameters = (array)$parameters;
        }

        $response = $this->http->put($this->requestUrl, $parameters);
        if ($response->successful()) {
            $headers      = ['headers' => $response->headers()];
            $responseData = is_array($response->json()) ? $response->json() : [];

            return array_merge($responseData, $headers, ['success' => true, 'statusCode' => $response->status()]);
        } else {
            return [
                'success'    => false,
                'data'       => $response->json(),
                'error_bag'  => $response->body(),
                'method'     => 'PUT',
                'url'        => $this->requestUrl,
                'statusCode' => $response->status(),
                'headers'    => $response->headers()
            ];
        }
    }

    /**
     * @param mixed $parameters
     * @return array
     */
    protected function deleteRequest(mixed $parameters): array
    {
        if (!is_array($parameters)) {
            $parameters = (array)$parameters;
        }

        $response = $this->http->delete($this->requestUrl, $parameters);

        if ($response->successful()) {
            $headers      = ['headers' => $response->headers()];
            $responseData = is_array($response->json()) ? $response->json() : [];

            return array_merge($responseData, $headers, ['success' => true, 'statusCode' => $response->status()]);
        } else {
            return [
                'success'    => false,
                'data'       => $response->json(),
                'error_bag'  => $response->body(),
                'method'     => 'DELETE',
                'url'        => $this->requestUrl,
                'statusCode' => $response->status(),
                'headers'    => $response->headers()
            ];
        }
    }

    /**
     * Extracts pagination information from HTTP headers.
     *
     * @param array $headers
     * @return array
     */
    protected function extractPageInfo(array $headers): array
    {
        if (!isset($headers['link'])) {
            return [
                'next'     => null,
                'previous' => null
            ];
        }

        return [
            'next'     => $this->getPageInfo($headers['link'][0], 'next'),
            'previous' => $this->getPageInfo($headers['link'][0], 'previous')
        ];
    }

    /**
     * Extracts the page information from a link header based on the specified relation type.
     *
     * @param string $linkHeader The Link header containing pagination information.
     * @param string $rel The relation type ('next', 'previous', etc.) to extract the page info for.
     * @return string|null Returns the page information if found, otherwise null.
     */
    private function getPageInfo(string $linkHeader, string $rel): ?string
    {
        preg_match('/page_info=([^&]+)>; rel="' . $rel . '"/', $linkHeader, $matches);

        return $matches[1] ?? null;
    }
}