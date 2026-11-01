<?php 
function prepareDataForCallback($paymentGateway,$method,$uncompleted_id,$action){
    $ci = & get_instance();

    $gatewayData = [];

    switch($paymentGateway){
        case 'flutterwave':
            $gatewayData['id'] = $_GET['tx_ref'];
            $gatewayData['redirect'] = base_url('store/thankyou/'. $_GET['tx_ref']);
        break;

        case 'opay':
            $gatewayData['id'] = $uncompleted_id;
        break;

        case 'paypal':
            if($method == 'notify'){
                $uncompletedData = $ci->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
                $content = explode(' || ',$uncompletedData['content']);
                $order = unserialize($content[0]);
                $products = unserialize($content[1]);
            
                $gatewayData['total'] = str_replace(',','',c_format($order['total'],false));
                $gatewayData['currency'] = $ci->session->userdata('userCurrency');
                $gatewayData['id'] = $uncompleted_id;
                $gatewayData['return_url'] = base_url('store/thankyou/'.$uncompleted_id);
                $gatewayData['cancel_url'] = base_url('store/checkout');
            } else {
                $gatewayData['cancel_url'] = base_url('store/checkout');
            }
        break;

        case 'paypalstandard':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('store/thankyou/'.$uncompleted_id);
        break;

        case 'paystack':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('store/thankyou/'.$uncompleted_id);
        break;

        case 'paytm':
            $gatewayData['redirect'] = base_url('store/thankyou/');
            $gatewayData['cancel'] = base_url('store/checkout');
        break;

        case 'razorpay':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('store/thankyou/'.$uncompleted_id);
        break;

        case 'xendit':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('store/thankyou/'.$uncompleted_id);
        break;

        case 'yookassa':
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['redirect'] = base_url('store/thankyou/'.$uncompleted_id);
        break;

        default:
            $gatewayData['method'] = $method;
            $gatewayData['id'] = $uncompleted_id;
            $gatewayData['action'] = $action;
            $gatewayData['return_url'] = base_url('store/thankyou/'.$uncompleted_id);
            $gatewayData['cancel_url'] = base_url('store/checkout');
    }

    return $gatewayData;
}
