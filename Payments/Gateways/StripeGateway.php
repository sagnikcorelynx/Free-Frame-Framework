<?php

namespace Payments\Gateways;

use Stripe\Stripe;
use Stripe\Charge;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function charge(array $data)
    {
        return Charge::create([
            'amount' => $data['amount'] * 100, // in cents
            'currency' => $data['currency'],
            'source' => $data['token'], // token from client
            'description' => $data['description'],
        ]);
    }
}