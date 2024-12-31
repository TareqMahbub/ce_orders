<?php

namespace App\Services;

use Exception;
use App\Traits\HasMakeAble;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CeService
{
    use HasMakeAble;

    /**
     * @param  array  $response
     * @return bool
     */
    private function isOk(array $response): bool
    {
        return array_key_exists('StatusCode', $response) && $response['StatusCode'] === 200;
    }

    private function isSuccess(array $response): bool
    {
        return array_key_exists('Success', $response) && $response['Success'] === true;
    }

    private function hasContent(array $response): bool
    {
        return array_key_exists('Content', $response) && is_array($response['Content']);
    }

    /**
     * @param  int  $page
     * @param  array  $statuses
     * @return array
     */
    public function fetchPagedOrders(int $page = 1, array $statuses = ['NEW']): array
    {
        $url = config('ce.api.endpoint') .  "/v2/orders";
        $params = [
            'apikey' => config('ce.api.key'),
            'statuses' => $statuses,
            'page' => $page
        ];

        if (($responseObject = Http::get($url, $params))->successful()) {
            $response = $responseObject->json();

            if ($this->isOk($response) && $this->isSuccess($response) && $this->hasContent($response)) {
                return $response['Content'];
            }
        }

        return [];
    }

    /**
     * @param  string  $merchantProductNo
     * @param  int  $stockLocationId
     * @param  int  $stock
     * @return bool
     */
    public function setStock(string $merchantProductNo, int $stockLocationId, int $stock): bool
    {
        try {
            $url = config('ce.api.endpoint') . "/v2/offer/stock";
            $params = ['apikey' => config('ce.api.key')];
            $data = [
                "MerchantProductNo" => $merchantProductNo,
                "StockLocations" => [
                    [
                        "Stock" => $stock,
                        "StockLocationId" => $stockLocationId
                    ]
                ]
            ];

            if (($responseObject = Http::withQueryParameters($params)->put($url, [$data]))->successful()) {
                $response = $responseObject->json();

                if ($this->isOk($response) && $this->isSuccess($response)) {
                    return true;
                }
            }
        } catch (ConnectionException|Exception $e) {
            $errorMessage = $e->getMessage() . ', Line: ' . $e->getLine() . ', File: ' . $e->getFile();
            Log::error(
                "Failed to update merchant product stock. Exception: $errorMessage",
                [
                    'merchant_product_no' => $merchantProductNo,
                    'stock_location_id' => $stockLocationId,
                    'stock' => $stock
                ]
            );
        }

        return false;
    }

    /**
     * @param  string  $merchantProductNo
     * @return array
     */
    public function getProduct(string $merchantProductNo): array
    {
        $url = config('ce.api.endpoint') .  "/v2/products/$merchantProductNo";
        $params = [
            'apikey' => config('ce.api.key'),
        ];

        if (($responseObject = Http::get($url, $params))->successful()) {
            $response = $responseObject->json();

            if ($this->isOk($response) && $this->isSuccess($response) && $this->hasContent($response)) {
                return $response['Content'];
            }
        }

        return [];
    }
}
