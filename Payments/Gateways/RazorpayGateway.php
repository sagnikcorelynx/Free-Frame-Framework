<?php

namespace Payments\Gateways;

use Razorpay\Api\Api;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function charge(array $data)
    {
        return $this->api->payment->fetch($data['payment_id'])->capture(['amount' => $data['amount'] * 100]);
    }
}