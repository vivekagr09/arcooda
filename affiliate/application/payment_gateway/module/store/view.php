<?php 
function prepareDataForView($paymentGateway,$uncompleted_id,$user,$order,$products){
    $ci = & get_instance();

    $gatewayData = [];

    switch($paymentGateway){
        case 'bank_transfer':
            $gatewayData['module'] = 'store';
        break;

        case 'cod':
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
        break;

        case 'flutterwave':
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['redirect_url'] = base_url('store/payment_gateway/flutterwave/callback');
            $gatewayData['email'] = $user['email'];
            $gatewayData['phone'] = $order['phone'];
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['title'] = 'My Store';
        break;    

        case 'opay':
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
        break;

        case 'paypal':
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
        break;

        case 'paypalstandard':
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['return'] = base_url('store/thankyou/'. $uncompleted_id);
            $gatewayData['notify_url'] = base_url('store/payment_gateway/paypalstandard/callback/'.$uncompleted_id);
            $gatewayData['cancel_return'] = base_url('store/checkout');
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
        break;

        case 'paystack':
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
            $gatewayData['calback'] = base_url("store/payment_gateway/paystack/update/".$uncompleted_id);
        break;

        case 'paytm':
            $gatewayData['email'] = $user['email'];
            $gatewayData['user_id'] = $user['id'];
            $gatewayData['callback_url'] = base_url('store/payment_gateway/paytm/callback');
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = $order['total'];
            $gatewayData['phone'] = $order['phone'];
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
        break;

        case 'razorpay':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false)) * 100;
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['callback_url'] = base_url('store/payment_gateway/razorpay/callback/'.$uncompleted_id);
            $gatewayData['email'] = $user['email'];
            $gatewayData['phone'] = $order['phone'];
            $gatewayData['address'] = $order['address'];
        break;

        case 'skrill':
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['return_url'] = base_url('store/thankyou/'. $uncompleted_id);
            $gatewayData['cancel_url'] = base_url('store/checkout');
            $gatewayData['status_url'] = base_url('store/payment_gateway/skrill/callback');
            $gatewayData['email'] = $user['email'];
            $gatewayData['address'] = $order['address'];
            $gatewayData['address2'] = '';
            $gatewayData['phone'] = $order['phone'];
            $gatewayData['zip_code'] = $order['zip_code'];
            $gatewayData['city'] = $order['city'];
            $gatewayData['state_name'] = $order['state_name'];
            $gatewayData['country_code'] = $order['country_code'];
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');;

            $gatewayData['detail1_text'] = '';
            foreach ($products as $product)
                $gatewayData['detail1_text'] .= $product['quantity'] . ' x ' . $product['product_name'] . ', ';

            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
        break;

        case 'stripe':
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['email'] = $user['email'];
            $gatewayData['address'] = $order['address'];
            $gatewayData['city'] = $order['city'];
            $gatewayData['state_name'] = $order['state_name'];
            $gatewayData['zip_code'] = $order['zip_code'];
            $gatewayData['country_code'] = $order['country_code'];
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['action'] = base_url('store/confirm_payment');
        break;

        case 'xendit':
            $gatewayData['status_url'] = base_url('store/payment_gateway/xendit/callback/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('store/checkout');
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
            $gatewayData['email'] = $user['email'];
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
        break;

        case 'yookassa':
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
        break;

        default:
            $gatewayData['module'] = 'store';
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['user'] = $user;
            $gatewayData['item'] = $order;
            $gatewayData['info'] = $products;
            $gatewayData['payment_confirmation'] = base_url("store/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("store/confirm_payment");
            $gatewayData['return_url'] = base_url('store/thankyou/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('store/checkout');
            $gatewayData['callback_url'] = base_url('store/payment_gateway');
    }

    return $gatewayData;
}

