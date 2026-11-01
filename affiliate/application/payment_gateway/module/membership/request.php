<?php 
function prepareDataForRequest($paymentGateway,$uncompleted_id,$user,$plan){
    $ci = & get_instance();

    $gatewayData = [];

    switch($paymentGateway){
        case 'bank_transfer':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['status'] = 0;
            $gatewayData['redirect'] = base_url('membership/changeUrlAfterSuccessPayment/'.$uncompleted_id);
        break;

        case 'opay':
            $gatewayData['module'] = 3;
            $gatewayData['redirect'] = base_url('membership/changeUrlAfterSuccessPayment/'.$uncompleted_id);
        break;

        case 'paypal':
            $gatewayData['return_url'] = base_url('membership/payment_gateway/paypal/notify/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('membership/payment_gateway/paypal/cancel/'.$uncompleted_id);

            $Payments = array();
            $PaymentOrderItems = array(
                'name'    => $plan->name,
                'desc'    => $plan->description,
                'amt'     => str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false)),
                'number'  => $plan->id,
                'qty'     => 1,
                'taxamt'  => 0,
                'itemurl' => '', 
            );
           
            $Payment = array(
                'order_items'  => $PaymentOrderItems,
                'amt'          => str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false)),
                'itemamt'      => str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false)),
                'currencycode' => $ci->session->userdata('userCurrency'),
            );

            array_push($Payments, $Payment);

            $gatewayData['payments'] = $Payments;
        break;

        case 'paystack':
            $gatewayData['currency'] = $ci->session->userdata('userCurrency');
            $gatewayData['email'] = $user['email'];
            $gatewayData['total'] = str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false))*100;
        break;

        case 'stripe':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['amount'] = str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false))*100;
            $gatewayData['currency'] = $ci->session->userdata['userCurrency'];
            $gatewayData['description'] = __('user.charge_for_membership');
            $gatewayData['metadata'] = array('plan_id' => $plan->id,'email' => $user->email);
            $gatewayData['redirect'] = base_url('membership/changeUrlAfterSuccessPayment/'.$uncompleted_id);
        break;

        case 'yookassa':
            $gatewayData['total'] = str_replace(',','',c_format($plan->special ? $plan->special : $plan->price,false));
            $gatewayData['return_url'] = base_url('membership/payment_gateway/yookassa/confirmation/'.$uncompleted_id);
            $gatewayData['id'] = $uncompleted_id;
        break;

        case 'toyyibpay':
            $gatewayData['total'] = str_replace(',','',c_format(($plan->special ? $plan->special : $plan->price),false));
             $gatewayData['return_url'] = base_url('membership/payment_gateway/toyyibpay/callback/'.$uncompleted_id);
            $gatewayData['email'] = $user['email'];
            $gatewayData['name'] = $user['firstname'];
            $gatewayData['phone'] = !empty($user->PhoneNumber)? $user->PhoneNumber  : (!empty($user->phone) ? $user->phone : '1234567890');
            $gatewayData['description'] = __('front.charge_for_membership');
            $gatewayData['id'] = $uncompleted_id;
             $gatewayData['redirect'] = base_url('membership/changeUrlAfterSuccessPayment/'.$uncompleted_id);
        break;

        default:
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['user'] = $user;
            $gatewayData['item'] = $plan;
            $gatewayData['info'] = '';
            $gatewayData['return_url'] = base_url('membership/changeUrlAfterSuccessPayment/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/purchase_plan');
            $gatewayData['callback_url'] = base_url('membership/payment_gateway');
    }

    return $gatewayData;
}
