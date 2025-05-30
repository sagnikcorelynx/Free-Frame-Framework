<?php

namespace Payments\Gateways;

class PaypalGateway implements PaymentGatewayInterface
{
    public function charge(array $data)
    {
        // You'll use PayPal SDK or API here. Placeholder:
        return ['status' => 'success', 'message' => 'PayPal payment simulated'];
    }
}


