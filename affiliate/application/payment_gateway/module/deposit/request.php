<?php 
function prepareDataForRequest($paymentGateway,$uncompleted_id,$user,$vendor_deposit){
    $ci = & get_instance();
    
    $gatewayData = [];

    $gatewayData = [];

    switch($paymentGateway){
        case 'bank_transfer':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['status'] = 7;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'opay':
            $gatewayData['module'] = 2;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;
        case 'paytm':
            $gatewayData['id'] = "OREDRID_".$uncompleted_id;

            $gatewayData['return_url'] = base_url('usercontrol/payment_gateway/paytm/notify/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/payment_gateway/paytm/cancel/'.$uncompleted_id);
            $gatewayData['callback_url'] = base_url('usercontrol/payment_gateway/paytm/callback/'.$uncompleted_id);

            $gatewayData['txnAmount'] = array(
                'value' => (string)str_replace(',','',c_format($vendor_deposit['vd_amount'],false)),
                'currency' => $ci->session->userdata('userCurrency'),
            );

            $gatewayData['userInfo'] = array(
                'email' => $user['email'],
                'custId' => "CUST_".$user['id'],
            );

        break;
        case 'paypal':
            $gatewayData['return_url'] = base_url('usercontrol/payment_gateway/paypal/notify/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/payment_gateway/paypal/cancel/'.$uncompleted_id);

            $Payments = array();

            $Payment = array(
                'amt'          => str_replace(',','',c_format($vendor_deposit['vd_amount'],false)),
                'itemamt'      => str_replace(',','',c_format($vendor_deposit['vd_amount'],false)),
                'currencycode' => $ci->session->userdata('userCurrency'),
            );
            array_push($Payments, $Payment);

            $gatewayData['payments'] = $Payments;
        break;

        case 'paystack':
        $gatewayData['currency'] = $ci->session->userdata('userCurrency');
            $gatewayData['email'] = $user['email'];
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false)) * 100;
        break;

        case 'stripe':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['amount'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false)) * 100;
            $gatewayData['currency'] = $ci->session->userdata['userCurrency'];
            $gatewayData['description'] = __('user.charge_for_vendor_deposit');
            $gatewayData['metadata'] = array('vendor_deposit_id' => $uncompleted_id);
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'yookassa':
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['return_url'] = base_url('usercontrol/payment_gateway/yookassa/confirmation/'.$uncompleted_id);
            $gatewayData['id'] = $uncompleted_id;
        break;

        case 'toyyibpay':   
            $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
            $gatewayData['return_url'] = base_url('usercontrol/payment_gateway/toyyibpay/callback/'.$uncompleted_id);
            $gatewayData['email'] = $user['email'];
            $gatewayData['name'] = $user['firstname'];
            $gatewayData['phone'] = !empty($user['PhoneNumber'])? $user['PhoneNumber']  : (!empty($user['phone'])? $user['phone'] : '1234567890');
            $gatewayData['description'] = __('front.charge_for_vendor_deposit');
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;
      

        default:
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['user'] = $user;
            $gatewayData['item'] = $vendor_deposit;
            $gatewayData['info'] = '';
            $gatewayData['return_url'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
            $gatewayData['callback_url'] = base_url('usercontrol/payment_gateway');
    }

    return $gatewayData;
}
