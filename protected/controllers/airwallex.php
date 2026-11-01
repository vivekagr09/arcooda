<?php
namespace app\controllers;

use app\models\TwigInvoice;
use app\models\Airwallex\Client;

/**
 * @var string             $action
 * @var \app\models\MyTwig $twig
 */
?><?php

switch ($action) {
    case 'invoice':
        global $_GLOBALS;

        $invoices = TwigInvoice::search([
            'invoice_id' => $_REQUEST['id'] ?? $_REQUEST['invoice_id'] ??  0,
            'rand_num'   => max($_REQUEST['crc'] ?? $_REQUEST['invoice_crc']  ?? 0, 1),
        ]);
        if(isset($_POST['invoice_id']) && isset($_POST['invoice_crc']) && str_contains($_SERVER['HTTP_REFERER'], 'payment/payment-checkout')){
             $invoices = TwigInvoice::search([
                'invoice_id' => $_POST['invoice_id'] ?? 0,
                'rand_num'   => max($_POST['invoice_crc'] ?? 0, 1),
            ]);
        }
        if (empty($invoices)) {
            redirect('/account/orders');
            die();
        }

        $twig_invoice = reset($invoices);
        $invoice_id   = $twig_invoice->id;
        $invoice = invoiceFromDatabase($invoice_id);
        if (!in_array($invoice['invoice_status'], array('proforma', 'sales'))) {
            redirect('/account/orders');
            die();
        }
        
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        $amount_due       = $invoice['invoice_offer'];
        foreach ($invoice_payments as $row) {
            $amount_due = bcsub($amount_due, $row['payment_amount'], 2);
        }
        if (bccomp($amount_due, '0.00', 2) <= 0) {
            redirect('/payment/success');
            die();
        }
        if(isset($_POST['invoice_total']) && $_POST['invoice_total']>0){
            $amount_due = $_POST['invoice_total'];
            invoiceSetValue($invoice_id,'paymenttype_id',$_POST['paymenttype_id']);
            $invoice['paymenttype_id'] = $_POST['paymenttype_id'];
            if(!empty($_POST['comment'])){
                $history                    = [];
                $history['message_sender']  =  $invoice['member_id'];
                $history['message_sentto']  = 'System Admin';
                $history['message_title']   = $invoice['invoice_number'] . ' payment';
                $history['message_type_id'] = 3;
                $history['message_notes']   = $_POST['comment'];
                $history['message_raw']     = $_POST['comment'];
                invoiceHistoryToDatabase($invoice_id, $history);
            }
            if (in_array($_POST['paymenttype_id'], ['BANK','USD_BANK']) && $invoice_id) {
                redirect('/payment_success?invoice=' . base64_encode((string)$invoice_id));
                die();
            }
        }
        $invoice_products = invoiceProductsFromDatabase($invoice_id);
        $invoice_charges  = invoiceChargesFromDatabase($invoice_id);

        $env = ($_GLOBALS['site_settings']['airwallex']['production'] ?? false) ? 'prod' : 'demo';

        $bill_names   = preg_split('/\\s+/', trim($invoice['bill_name']), 2);
        $ship_names   = preg_split('/\\s+/', trim($invoice['ship_name']), 2);
        $country_list = invoiceGetOptionList('bill_country');

        $customer = [
            'merchant_customer_id' => $invoice['member_id'],
            'address'              => [
                'street'       => (!empty(trim($invoice['bill_address'])))?$invoice['bill_address']:'3rd street',
                'city'         => $invoice['bill_city'],
                'state'        => $invoice['bill_state'],
                'country_code' => $invoice['bill_country'],
                'postcode'     => trim($invoice['bill_postcode']),
            ],
            'phone_number'         => $invoice['bill_phone'],
            'email'                => $invoice['bill_email'],
        ];
        if (!empty($invoice['bill_company'])) {
            $customer['business_name'] = $invoice['bill_company'];
        } else {
            $customer['first_name'] = reset($bill_names);
            $customer['last_name']  = end($bill_names);
        }

        $discount_total = '0.00';
        $sub_total      = '0.00';

        $order = [
            'products' => [],
            'shipping' => [
                'first_name'      => reset($ship_names),
                'last_name'       => end($ship_names),
                'address'         => [
                    'street'       => (!empty(trim($invoice['ship_address'])))?$invoice['ship_address']:'3rd street',
                    'city'         => $invoice['ship_city'],
                    'state'        => $invoice['ship_state'],
                    'postcode'     => trim($invoice['ship_postcode']),
                    'country_code' => $invoice['ship_country'],
                ],
                'phone_number'    => $invoice['ship_phone'],
                'shipping_method' => $invoice['ship_product_name'],
            ],
        ];
      foreach ($invoice_products as $invoice_product) {
            $sub_total = bcadd($sub_total, bcmul(
                (string)max(1, $invoice_product['product_qty']),
                $invoice_product['product_offer'],
                2
            ), 2);
            /*if($invoice_product['colour_id']>0){
                $invoice_product['product_name']=$invoice_product['product_name'].' ('.$invoice_product['colour_name'].'-'.$invoice_product['product_id'].$invoice_product['colour_code']   .')';
            }
            $order['products'][] = [
                'code'       => $invoice_product['product_number'],
                'name'       => $invoice_product['product_name'],
                'desc'       => $invoice_product['product_notes'],
                'quantity'   => max(1, $invoice_product['product_qty']),
                'unit_price' => abs($invoice_product['product_offer']),
            ];*/
        }
        if ('' != $invoice['ship_product_name']) {
            if($invoice['ship_offer']>0){
                $sub_total = bcadd($sub_total, $invoice['ship_offer'], 2);
                /*$order['products'][] = [
                    'code'       => $invoice['ship_product_str'],
                    'name'       => $invoice['ship_product_name'],
                    'desc'       => $invoice['ship_product_notes'],
                    'quantity'   => 1,
                    'unit_price' => abs($invoice['ship_offer']),
                ];*/
            }
        }
        foreach ($invoice_charges as $invoice_charge) {
            $sub_total = bcadd($sub_total, bcmul(
                (string)max(1, $invoice_charge['charge_qty']),
                $invoice_charge['charge_offer'],
                2
            ), 2);
           /* $order['products'][] = [
                'code'       => $invoice_charge['charge_number'],
                'name'       => $invoice_charge['charge_name'],
                'desc'       => $invoice_charge['charge_notes'],
                'quantity'   => max(1, $invoice_charge['charge_qty']),
                'unit_price' => abs($invoice_charge['charge_offer']),
            ];*/
        }
        $handling = bcsub($amount_due, $sub_total, 2);

        $client = new Client($_GLOBALS['site_settings']['airwallex']);
        $order['products'][] = [
             'name'       => 'INVOICE  '.$invoice['invoice_number'],
             'quantity'   => '1',
             'unit_price' => $amount_due,
        ];
        $intent = $client->paymentIntent->create([
            'amount'   => $amount_due,
            'currency' => $invoice['invoice_currency'],
            'customer' => $customer,
            'merchant_order_id' => sprintf(
                '%-27.27s(%05d)',
                preg_replace('/[\0-\x1f\x80-\xff]+/', '.', sprintf(
                    '%s - %s',
                    $invoice['invoice_number'],
                    $invoice['ship_name']
                )),
                $invoice['member_id']
            ),
            'order' => $order,
            "request_id"             => '00e8df19-7087-4209-b50b-' . uniqid(),
            "payment_method_options" => [
                "card" => [
                    "three_ds_action"     => 'FORCE_3DS',
                    "allowedCardNetworks" => ['visa'],
                ],
            ],
        ]);
        $options = [
            'intent_id'     => $intent[1]->id,
            'client_secret' => $intent[1]->client_secret,
            'mode'          => 'payment',
            'env'           => $env,
            'currency'      => $invoice['invoice_currency'],
            'autoCapture'   => true,
            'successUrl'=>$_GLOBALS['site_settings']['home_uri'].'payment_success?invoice=' . base64_encode((string)$invoice_id),
            'failUrl'=>$_GLOBALS['site_settings']['home_uri'].'payment_fail?status=fail&invoice=' . base64_encode((string)$invoice_id),
            'logoUrl'=>$_GLOBALS['site_settings']['home_uri'].'/brand.png',
            'locale'        => 'en',
            'showTermLink'  => true,
            'country_code'  => $invoice['bill_country'],
            'shopper_name'  => $invoice['bill_name'],
            'shopper_phone' => $invoice['bill_phone'],
            'shopper_email' => $invoice['bill_email'],
        
            'applePayRequestOptions' => [
                'countryCode'     => 'AU',
            ],
        
            'googlePayRequestOptions' => [
                'countryCode' => 'AU',
                'buttonType'  => 'pay',
            ],
        ];
        switch ($invoice['paymenttype_id']) {
            case "GOOGLE":        // Google Pay
                $options['methods'] = ['googlepay'];
                break;

            case "APPLE":         // Apple Pay
                $options['methods'] = ['applepay'];
                break;

            case "AMEX":          // Credit Card - American Express
                //no break
            case "MASTERCARD":    // Credit Card - Mastercard
                //no break
            case "VISA":          // Credit Card - Visa
                //no break
            case "CREDIT":        // Credit Card - Visa/Mastercard
                //no break
            case "CREDIT_SECURE": // Secure Credit Card
                $options['methods'] = ['card'];
                break;
        
            /*
            case "":
                $options['methods'] = ['wechatpay'];
                break;
            */

            case "PAYPAL":        // PayPal
                $options['methods'] = ['paypal'];
                break;

            case "BANK":          // AUD Bank Deposit
                //no break
            case "EUR_BANK":      // EUR Bank Deposit
                //no break
            case "EUR_BANK2":     // EUR Bank Account - HSBC
                //no break
            case "GBP_BANK":      // GBP Bank Deposit
                //no break
            case "GBP_BANK2":     // GBP Bank Account - HSBC
                //no break
            case "HKD_BANK":      // HKD Bank Deposit
                //no break
            case "JPY_BANK":      // JPY Bank Deposit
                //no break
            case "USD_BANK3":     // USD Bank Account - Commonwealth Bank
                //no break
            case "USD_BANK":      // USD Bank Deposit - HSBC
                //no break
            case "USD_BANK2":     // Wise - International Customer
                //no break
            case "USD_BANK4": 
            case "CREDIT_VC":
            case "CREDIT_MC":
            case "CREDIT_AX":    // Wise - USA Customer
                $options['methods'] = $intent[1]->available_payment_method_types;
                break;
        }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <script src="https://checkout.airwallex.com/assets/elements.bundle.min.js"></script>
        <!-- <script src="https://checkout.airwallex.com/assets/elements.bundle.min.js"></script> -->
    </head>
    <body>
        <script type="text/javascript">
            /*<![CDATA[*/
            Airwallex.init(<?=json_encode([
                'env'    => $env,
                'origin' => rtrim('/', $_GLOBALS['site_settings']['home_uri']),
                 'theme'=> [
                    'fonts'=> [
                        [
                           'src'=>'https://checkout.airwallex.com/fonts/CircularXXWeb/CircularXXSub-BoldSubset.woff2',
                           'family'=> 'AxLLCircular',
                           'weight'=> '700',
                         ],
                    ],
                ]
            ])?>);
            Airwallex.redirectToCheckout(<?=json_encode($options)?>);
            /*]]>*/
        </script>
        Redirecting to merchant facility...
    </body>
</html>
<?php
        die();

    case 'cancel':
        //no break
    case 'fail':
        //no break
    case 'success':
        global $_GLOBALS;

        $client = new Client($_GLOBALS['site_settings']['airwallex']);
        
        $intent = $client->paymentIntent->get($_GET['id']);

        switch ($intent[1]->status) {
            case 'SUCCEEDED':
                //redirect('/payment/success');
                die();

            case 'CANCELLED':
                //redirect('/payment/cancel');
                die();

            case 'PENDING':
                //no break
            case 'REQUIRES_CAPTURE':
                //no break
            case 'REQUIRES_CUSTOMER_ACTION':
                //no break
            case 'REQUIRES_PAYMENT_METHOD':
                //no break
            default:
                //redirect('/payment/fail');
                die();
        }

        redirect('/payment/fail');
        die();

    case 'webhook':
        if (!in_array($_SERVER['REMOTE_ADDR'], [
            //For Production Environment
            '35.240.218.67',
            '35.185.179.53',
            '34.87.64.173',
            '35.220.213.251',
            '34.92.128.176',
            '47.91.227.149',
            '47.52.78.97',

            //For Demo Environment
            '35.240.211.132',
            '35.187.239.216',
            '34.87.139.23',
            '34.92.48.104',
            '34.92.144.250',
            '34.92.15.70',
            '8.210.222.213',
            '47.75.169.138',
        ])) {
            die();
        }
        break;
    case 'manual-payment' :
        global $_GLOBALS;
        global $DB;
        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`credit_payments` WHERE `credit_id` = %d LIMIT 1",
            $_GET['credit_id']
        ), MYSQLI_ASSOC));
        if (empty($row)) {
            redirect('/account/orders');
            die();
        }
        $amount_due = $row['credit_amount'];

        $env = ($_GLOBALS['site_settings']['airwallex']['production'] ?? false) ? 'prod' : 'demo';

        $bill_names   = preg_split('/\\s+/', trim($row['credit_name']), 2);
        $ship_names   = preg_split('/\\s+/', trim($row['credit_name']), 2);
        $country_list = getCountryInfo($row['credit_ip'])["a2"] ?? 'USA';

        $customer = [
            'merchant_customer_id' => $_GET['member_id'],
            'address'              => [
                'street'       => '3rd street',
                'country_code' => getCountryInfo($row['credit_ip'])["a2"] ?? 'USA',
            ],
            'phone_number'         => '',
            'email'                => $row['credit_email'],
        ];
        $customer['first_name'] = $row['credit_name'];
        $discount_total = '0.00';
        $sub_total      = '0.00';

        $order = [
            'products' => [],
            'shipping' => [
                'first_name'      => $row['credit_name'],
                'last_name'       =>'',
                'address'         => [
                    'street'       => '3rd street',
                    'city'         =>'',
                    'state'        =>'',
                    'country_code' => getCountryInfo($row['credit_ip'])["a2"] ?? 'USA',
                ],
                'phone_number'    => '',
                'shipping_method' => 'no shipping',
            ],
        ];

        $handling =$amount_due;

        $client = new Client($_GLOBALS['site_settings']['airwallex']);
        $order['products'][] = [
            'name'       => 'Manual Payment  '.$_GET['payment_id'],
             'quantity'   => '1',
             'unit_price' => $amount_due,
        ];
        $intent = $client->paymentIntent->create([
            'amount'   => $amount_due,
            'currency' => $row['credit_currency'],
            'customer' => $customer,
            'merchant_order_id' => sprintf(
                '%-27.27s(%05d)',
                preg_replace('/[\0-\x1f\x80-\xff]+/', '.', sprintf(
                    '%s - %s',
                    $_GET['payment_id'],
                    $row['credit_name']
                )),
                $_GET['member_id']
            ),
            'order' => $order,
            "request_id"             => '00e8df19-7087-4209-b50b-' . uniqid(),
            "payment_method_options" => [
                "card" => [
                    "three_ds_action"     => 'FORCE_3DS',
                    "allowedCardNetworks" => ['visa'],
                ],
            ],
        ]);
        $options = [
            'intent_id'     => $intent[1]->id,
            'client_secret' => $intent[1]->client_secret,
            'mode'          => 'payment',
            'env'           => $env,
            'currency'      => $row['credit_currency'],
            'autoCapture'   => true,
            'successUrl'=>$_GLOBALS['site_settings']['home_uri'].'payment_manual_success?credit_id='. base64_encode((int)$row['credit_id']).'&payment_id=' . base64_encode((int)$_GET['payment_id']),
            'failUrl'=>$_GLOBALS['site_settings']['home_uri'].'payment_manual_fail?status=fail&credit_id='.base64_encode((int)$row['credit_id']).'&payment_id=' . base64_encode((int)$_GET['payment_id']),
            'logoUrl'=>$_GLOBALS['site_settings']['home_uri'].'/brand.png',
            'locale'        => 'en',
            'showTermLink'  => true,
            'shopper_name'  => $row['credit_name'],
            'shopper_email' => $row['credit_amount'],
        
            'applePayRequestOptions' => [
                'countryCode'     => 'AU',
            ],
        
            'googlePayRequestOptions' => [
                'countryCode' => 'AU',
                'buttonType'  => 'pay',
            ],
        ];
        switch ($row['paymenttype_id']) {
            case "GOOGLE":        // Google Pay
                $options['methods'] = ['googlepay'];
                break;

            case "APPLE":         // Apple Pay
                $options['methods'] = ['applepay'];
                break;

            case "AMEX":          // Credit Card - American Express
                //no break
            case "MASTERCARD":    // Credit Card - Mastercard
                //no break
            case "VISA":          // Credit Card - Visa
                //no break
            case "CREDIT":        // Credit Card - Visa/Mastercard
                //no break
            case "CREDIT_SECURE": // Secure Credit Card
                $options['methods'] = ['card'];
                break;
        
            /*
            case "":
                $options['methods'] = ['wechatpay'];
                break;
            */

            case "PAYPAL":        // PayPal
                $options['methods'] = ['paypal'];
                break;

            case "BANK":          // AUD Bank Deposit
                //no break
            case "EUR_BANK":      // EUR Bank Deposit
                //no break
            case "EUR_BANK2":     // EUR Bank Account - HSBC
                //no break
            case "GBP_BANK":      // GBP Bank Deposit
                //no break
            case "GBP_BANK2":     // GBP Bank Account - HSBC
                //no break
            case "HKD_BANK":      // HKD Bank Deposit
                //no break
            case "JPY_BANK":      // JPY Bank Deposit
                //no break
            case "USD_BANK3":     // USD Bank Account - Commonwealth Bank
                //no break
            case "USD_BANK":      // USD Bank Deposit - HSBC
                //no break
            case "USD_BANK2":     // Wise - International Customer
                //no break
            case "USD_BANK4": 
            case "CREDIT_VC":
            case "CREDIT_MC":
            case "CREDIT_AX":    // Wise - USA Customer
                $options['methods'] = $intent[1]->available_payment_method_types;
                break;
        }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <script src="https://checkout.airwallex.com/assets/elements.bundle.min.js"></script>
        <!-- <script src="https://checkout.airwallex.com/assets/elements.bundle.min.js"></script> -->
    </head>
    <body>
        <script type="text/javascript">
            /*<![CDATA[*/
            Airwallex.init(<?=json_encode([
                'env'    => $env,
                'origin' => rtrim('/', $_GLOBALS['site_settings']['home_uri']),
                 'theme'=> [
                    'fonts'=> [
                        [
                           'src'=>'https://checkout.airwallex.com/fonts/CircularXXWeb/CircularXXSub-BoldSubset.woff2',
                           'family'=> 'AxLLCircular',
                           'weight'=> '700',
                         ],
                    ],
                ]
            ])?>);
            Airwallex.redirectToCheckout(<?=json_encode($options)?>);
            /*]]>*/
        </script>
        Redirecting to merchant facility...
    </body>
</html>
<?php
        die();

}
