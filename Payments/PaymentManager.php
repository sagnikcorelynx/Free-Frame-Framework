<?php
namespace Payments;

use App\Payments\Gateways\StripeGateway;
use App\Payments\Gateways\RazorpayGateway;
use App\Payments\Gateways\PaypalGateway;
use App\Payments\Gateways\SquareGateway;

class PaymentManager
{
    protected $gateway;

    public function __construct(string $gatewayName)
    {
        $gateways = [
            'stripe'    => StripeGateway::class,
            'razorpay'  => RazorpayGateway::class,
            'paypal'    => PaypalGateway::class,
            'square'    => SquareGateway::class,
        ];

        if (!isset($gateways[$gatewayName])) {
            throw new \Exception("Unsupported payment gateway: {$gatewayName}");
        }

        $this->gateway = new $gateways[$gatewayName]();
    }

    public function gateway()
    {
        return $this->gateway;
    }
}