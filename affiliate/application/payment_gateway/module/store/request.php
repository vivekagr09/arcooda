<?php 
function prepareDataForRequest($paymentGateway,$uncompleted_id,$user,$order,$products){
    $ci = & get_instance();

    $gatewayData = [];
$redirectCallbackURL =( isset($_SESSION['guestFlowClassified']) && $_SESSION['guestFlowClassified'] =='classified')  ?  base_url('store/thankyou/'.encryptString($uncompleted_id)) :  base_url('store/thankyou/'.$uncompleted_id);


    switch($paymentGateway){
        case 'bank_transfer':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['status'] = 7;
            $gatewayData['redirect'] = $redirectCallbackURL;
        break;

        case 'cod':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = $redirectCallbackURL;
        break;

        case 'opay':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = $redirectCallbackURL;
        break;

        case 'paypal':
            $gatewayData['return_url'] = base_url('store/payment_gateway/paypal/notify/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('store/payment_gateway/paypal/cancel/'.$uncompleted_id);

            $Payments = array();

            $PaymentOrderItems = array();
            foreach($products as $key => $product){
                $total = ($key == 0) ? ($product['total'] + $order['shipping_cost'] + $order['tax_cost']) : $product['total'];
                $Item = array(
                    'name'    => $product['product_name'],
                    'desc'    => $product['product_name'],
                    'amt'     => str_replace(',','',c_format($total,false)),
                    'number'  => $product['product_id'],
                    'qty'     => 1,
                    'taxamt'  => 0,
                    'itemurl' => '', 
                );
                array_push($PaymentOrderItems, $Item);
            }
           
            $Payment = array(
                'order_items'  => $PaymentOrderItems,
                'amt'          => str_replace(',','',c_format($order['total'],false)),
                'itemamt'      => str_replace(',','',c_format($order['total'],false)),
                'currencycode' => $ci->session->userdata('userCurrency'),
            );
            array_push($Payments, $Payment);

            $gatewayData['payments'] = $Payments;
        break;

        case 'paystack':
            $gatewayData['currency'] = $ci->session->userdata('userCurrency');
            $gatewayData['email'] = $user['email'];
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false)) * 100;
        break;

        case 'stripe':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['amount'] = str_replace(',','',c_format($order['total'],false)) * 100;
            $gatewayData['currency'] = $ci->session->userdata('userCurrency');
            $gatewayData['description'] = __('front.charge_for_order');
            $gatewayData['metadata'] = array('order_id' => $uncompleted_id,'email' => $order['email']);
            $gatewayData['redirect'] = $redirectCallbackURL;
        break;

        case 'yookassa':
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['return_url'] = base_url('store/payment_gateway/yookassa/confirmation/'.$uncompleted_id);
            $gatewayData['id'] = $uncompleted_id;
        break;

        case 'toyyibpay':
            $gatewayData['amount'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
             $gatewayData['return_url'] = base_url('store/payment_gateway/toyyibpay/callback/'.$uncompleted_id);
            $gatewayData['email'] = $user['email'];
            $gatewayData['name'] = $user['firstname'];
            $gatewayData['phone'] = $user['PhoneNumber']??$user['phone'];
            $gatewayData['description'] = __('front.charge_for_order');
            $gatewayData['id'] = $uncompleted_id;
             $gatewayData['redirect'] = $redirectCallbackURL;
        break;

        default:
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['user'] = $user;
            $gatewayData['item'] = $order;
            $gatewayData['info'] = $products;
            $gatewayData['return_url'] = $redirectCallbackURL;
            $gatewayData['cancel_url'] = base_url('store/checkout');
            $gatewayData['callback_url'] = base_url('store/payment_gateway');
    }

    return $gatewayData;
}
