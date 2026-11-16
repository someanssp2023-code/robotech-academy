<?php

namespace App\Services;

use GuzzleHttp\Client;

class KhqrService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['verify' => false, 'timeout' => 10]);
    }

    public function generateQR(float $amount, string $orderId): array
    {
        $response = $this->client->post(
            config('services.bakong.api_url') . '/v1/generate_qr',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.bakong.token'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'merchantID'   => config('services.bakong.account_id'),
                    'merchantName' => config('services.bakong.merchant_name'),
                    'merchantCity' => config('services.bakong.merchant_city'),
                    'amount'       => $amount,
                    'currency'     => 'USD',
                    'billNumber'   => $orderId,
                ],
            ]
        );
        return json_decode($response->getBody(), true);
    }

    public function checkPayment(string $md5): array
    {
        $response = $this->client->post(
            config('services.bakong.api_url') . '/v1/check_transaction_by_md5',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.bakong.token'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => ['md5' => $md5],
            ]
        );
        return json_decode($response->getBody(), true);
    }
}