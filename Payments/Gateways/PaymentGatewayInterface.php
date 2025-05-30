<?php

namespace Payments\Gateways;

interface PaymentGatewayInterface
{
    public function charge(array $data);
}