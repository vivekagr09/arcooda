<?php 
function prepareDataForCallback($paymentGateway,$method,$uncompleted_id,$action){
    $ci = & get_instance();

    $gatewayData = [];

    switch($paymentGateway){
        case 'flutterwave':
            $gatewayData['id'] = $_GET['tx_ref'];
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='. $_GET['tx_ref']);
        break;

        case 'opay':
            $gatewayData['module'] = 3;
        break;

        case 'paypal':
            if($method == 'notify'){
                $uncompletedData = $ci->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
                $content = explode(' || ',$uncompletedData['content']);
                $vendor_deposit = unserialize($uncompletedData['content']);
            
                $gatewayData['total'] = str_replace(',','',c_format($vendor_deposit['vd_amount'],false));
                $gatewayData['currency'] = $ci->session->userdata('userCurrency');
                $gatewayData['id'] = $uncompleted_id;
                $gatewayData['return_url'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
                $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits');
            } else {
                $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits');
            }
        break;

        case 'paypalstandard':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'paystack':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'paytm':
            $gatewayData['redirect'] = base_url('usercontrol/changeUrlAfterSuccessPayment/');
            $gatewayData['cancel'] = base_url('usercontrol/purchase_plan');
        break;

        case 'razorpay':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'xendit':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        case 'yookassa':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
        break;

        default:
            $gatewayData['method'] = $method;
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['action'] = $action;
            $gatewayData['return_url'] = base_url('usercontrol/my_deposits?vd='.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('usercontrol/my_deposits?vdc=1&vd='.$uncompleted_id);
    }

    return $gatewayData;
}
