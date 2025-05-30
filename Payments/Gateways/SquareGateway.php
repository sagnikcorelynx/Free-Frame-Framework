<?php

namespace Payments\Gateways;

use Square\SquareClient;
use Square\Models\CreatePaymentRequest;
use Square\Exceptions\ApiException;

class SquareGateway implements PaymentGatewayInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new SquareClient([
            'accessToken' => env('SQUARE_ACCESS_TOKEN'),
            'environment' => env('SQUARE_ENV', 'sandbox'), // or 'production'
        ]);
    }

    public function charge(array $data)
    {
        try {
            $paymentsApi = $this->client->getPaymentsApi();

            $request = new CreatePaymentRequest(
                $data['source_id'],
                uniqid(),
                new \Square\Models\Money($data['amount'] * 100, 'USD')
            );

            return $paymentsApi->createPayment($request);
        } catch (ApiException $e) {
            return $e->getMessage();
        }
    }
}