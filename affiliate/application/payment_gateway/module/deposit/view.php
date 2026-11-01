<?php 
function prepareDataForView($paymentGateway,$uncompleted_id,$user,$vendor_deposit){
    $ci = & get_instance();

    $gatewayData = [];

    $gatewayData = [];

    switch($paymentGateway){
        case 'bank_transfer':
            $gatewayData['module'] = 'deposit';
        break;

        case 'flutterwave':
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['redirect_url'] = base_url('usercontrol/payment_gateway/flutterwave/callback');
            $gatewayData['email'] = $user['email'];
            $gatewayData['phone'] = $user['phone'];
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['title'] = 'Affiliate';
        break;    

        case 'opay':
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment");
        break;

        case 'paypal':
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment");
        break;

        case 'paypalstandard':
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['return'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);;
            $gatewayData['notify_url'] = base_url('usercontrol/payment_gateway/paypalstandard/callback/'.$uncompleted_id);
            $gatewayData['cancel_return'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
        break;

        case 'paystack':
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment");
            $gatewayData['calback'] = base_url("usercontrol/payment_gateway/paystack/update/".$uncompleted_id);
        break;

        case 'paytm':
            $gatewayData['email'] = $user['email'];
            $gatewayData['user_id'] = $user['id'];
            $gatewayData['callback_url'] = base_url('usercontrol/payment_gateway/paytm/callback');
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = $vendor_deposit['vd_amount'];
            $gatewayData['phone'] = $user['phone'];
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment");

        break;

        case 'razorpay':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false)) * 100;
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['callback_url'] = base_url('usercontrol/payment_gateway/razorpay/callback/'.$uncompleted_id);
            $gatewayData['email'] = $user['email'];
            $gatewayData['phone'] = $user['phone'];
            $gatewayData['address'] = '';
        break;

        case 'skrill':
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['return_url'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
            $gatewayData['status_url'] = base_url('usercontrol/payment_gateway/skrill/callback');
            $gatewayData['email'] = $user['email'];
            $gatewayData['address'] = $user['address'];
            $gatewayData['address2'] = '';
            $gatewayData['phone'] = $user['phone'];
            $gatewayData['zip_code'] = $user['zip_code'];
            $gatewayData['city'] = $user['city'];
            $gatewayData['state_name'] = $user['state_name'];

            if($user['Country'])
                $country = $ci->db->query('SELECT sortname FROM countries WHERE id='.$user['Country'])->row();
            $gatewayData['country_code'] = ($user['Country']) ? $country->sortname : '';

            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['currency_code'] = $ci->session->userdata('userCurrency');
            $gatewayData['detail1_text'] = __('user.charge_for_vendor_deposit').'#'.$uncompleted_id.' '.$user['firstname'].' '.$user['lastname'];

            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
        break;

        case 'stripe':
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['email'] = $user['email'];
            $gatewayData['address'] = '';
            $gatewayData['city'] = '';
            $gatewayData['state_name'] = '';
            $gatewayData['zip_code'] = '';
            $gatewayData['country_code'] = $user['country'];
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['action'] = base_url('usercontrol/confirm_payment');
        break;

        case 'xendit':
            $gatewayData['status_url'] = base_url('usercontrol/payment_gateway/xendit/callback/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['email'] = $user['email'];
            $gatewayData['firstname'] = $user['firstname'];
            $gatewayData['lastname'] = $user['lastname'];
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
        break;

        case 'yookassa':
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment"); 
        break;

        default:
            $gatewayData['module'] = 'deposit';
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['user'] = $user;
            $gatewayData['item'] = $vendor_deposit;
            $gatewayData['info'] = '';
            $gatewayData['payment_confirmation'] = base_url("usercontrol/payment_confirmation");
            $gatewayData['confirm_payment'] = base_url("usercontrol/confirm_payment");
            $gatewayData['return_url'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
            $gatewayData['callback_url'] = base_url('usercontrol/payment_gateway');
    }

    return $gatewayData;
}
