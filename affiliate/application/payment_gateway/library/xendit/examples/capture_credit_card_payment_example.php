<?php
    require('config/xendit_php_client_config.php');
    require('XenditPHPClient.php');

    $options['secret_api_key'] = constant('SECRET_API_KEY');

    $xenditPHPClient = new XenditClient\XenditPHPClient($options);

    $external_id = '5f43f4aa28971e001baa93df';
    $token_id = $argv[2];
    $amount = $argv[3];

    $response = $xenditPHPClient->captureCreditCardPayment($external_id, $token_id, $amount);

    print_r($response);
?>
