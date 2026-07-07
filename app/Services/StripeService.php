<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    protected function setApiKey()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent($amount, $currency)
    {
        $this->setApiKey();

        $pi = PaymentIntent::create([
            'amount'   => $amount * 100,
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ]
        ]);
        
        // Return both payment intent ID and client secret
        return [
            'payment_intent_id' => $pi->id,
            'client_secret' => $pi->client_secret
        ];
    }

    public function verifyPaymentIntent($paymentIntentId)
    {
        $this->setApiKey();
        
        // Retrieve the PaymentIntent
        $pi = PaymentIntent::retrieve($paymentIntentId);
        
        // Check if payment was successful
        if ($pi->status !== 'succeeded') {
            throw new \Exception('Payment not completed. Status: ' . $pi->status);
        }
        
        return $pi;
    }
}
