<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class StockService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://yahoo-finance-api-data.p.rapidapi.com'
        ]);
        $this->apiKey = env('YAHOO_FINANCE_API_KEY');
    }

    public function getStockPrice($symbol)
    {
        try {
            // Kirim request GET ke Yahoo Finance API melalui RapidAPI
            $response = $this->client->request('GET', '/stock/v2/get-summary', [
                'query' => ['symbol' => $symbol],
                'headers' => [
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com',
                ],
            ]);

            $data = json_decode($response->getBody(), true);  // Decode respons JSON menjadi array

            // Cek apakah respons berisi data saham
            if (isset($data['price'])) {
                return $data['price'];
            } else {
                Log::error("Error: Missing price data in API response", $data);
                return ['error' => 'Missing price data'];
            }
        } catch (RequestException $e) {
            Log::error("API Request Error: " . $e->getMessage());
            return ['error' => 'API Request Error'];
        } catch (\Exception $e) {
            Log::error("General Error: " . $e->getMessage());
            return ['error' => 'General Error'];
        }
    }
}
