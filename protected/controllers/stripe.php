<?php
namespace app\controllers;

use app\models\TwigCurrency;
use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 * @var string             $action
 * @var \app\models\MyTwig $twig
 */
?><?php

switch ($action) {
    case 'index':

    case 'result':
        Stripe::setApiKey('sk_test_16EI0qL0OKM3UT2Rwc5Oovxq00Tq70GT37');
        header('Content-Type: application/json');
        $json_str = file_get_contents('php://input') ?: 'null';
        $json_obj = json_decode($json_str);
        $intent   = null;

        // Get total amount
        $totalAmount = 0;
        foreach ($cart_items as $item) {
            $totalAmount += $item['price'];
        }

        // Get currency
        $currency = (new TwigCurrency())->getCurrency();
        try {
            if (isset($json_obj->payment_method_id)) {
                $intent = PaymentIntent::create([
                    'payment_method'      => $json_obj->payment_method_id,
                    'amount'              => $totalAmount * 100,
                    'currency'            => strtolower($currency),
                    'confirmation_method' => 'manual',
                    'confirm'             => true,
                ]);
            }
            if (isset($json_obj->payment_intent_id)) {
                $intent = PaymentIntent::retrieve(
                    $json_obj->payment_intent_id
                );
                $intent->confirm();
            }

            if ('requires_action' == $intent->status && 'use_stripe_sdk' == $intent->next_action->type) {
                echo json_encode([
                    'requires_action'              => true,
                    'payment_intent_client_secret' => $intent->client_secret,
                ]);
            } else if ('succeeded' == $intent->status) {
                echo json_encode([
                    "success" => true,
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Invalid PaymentIntent status']);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo json_encode([
                'error' => $e->getMessage(),
            ]);
        }
        die();
}
