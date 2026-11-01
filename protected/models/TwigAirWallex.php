<?php

namespace app\models;

class TwigAirWallex 
{
    const API_URL = 'https://api.airwallex.com/api/v1'; 

    const TEST_API_URL = 'https://api-demo.airwallex.com/api/v1';

    private $apiKey;

    private $clientId;

    private $config;

    private $factory;

    public function __construct($config = [])
    {
        $this->config = [
            'clientId' => 'ykHIr7I_RrquDo5sakLptg', 
            'apiKey' => 'd36535431ac08ed9a759996bcd5f45e6a57591b53f73146ef730abf10d0bb22a22d9dd0c29385e2d5229e083b7b2c56e',
            'production' => true,
        ];

        foreach ($config as $key => $item) 
        {
            switch ($key) {
                case 'clientId':
                    $this->clientId = $item;
                    break;
                case 'apiKey':
                    $this->apiKey = $item;
                    break;
                default:
                    $config[$key] = $item;
                    break;
            }
        }
    }

    //execute curl
    public function request($method, $path, $params = [])
    {
        $token = $this->authenticate();

        $headers = [
            'Authorization' => $token
        ];
        $response = $this->curl($method, $path, $headers, $params);

        return $response;
    }

    protected function curl($method, $url, $headers = [], $params = [])
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);
        $curlHeaders = [];
        
        foreach ($headers as $key => $header)
        {
            if ('Authorization' === $key)
            {
                $curlHeaders[] = "$key: Bearer ".$header;
            }
            else
            {
                $curlHeaders[] = "$key: ".$header;
            }
        }

        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                $params = json_encode($params);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case "GET":
                if ($params['id']) 
                {
                    $url = $url . '/' . $params['id'];
                }
                
                unset($params['id']);

                if (count($params) > 0)
                {
                    $url  = $url . '?' . http_build_query($params);
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $this->getApiUrl().$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);

        $result = json_decode(curl_exec($curl));
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        return [$code, $result];
    }

    protected function getApiUrl()
    {
        return (true === $this->config['production'] ? self::API_URL : self::TEST_API_URL);
    }

    protected function authenticate()
    {
        //prepare credentials (login)
        $headers = [
            'x-client-id' => $this->getClientId(),
            'x-api-key' => $this->getApiKey(),
        ];
        
        list($code, $response) = $this->curl('POST', '/authentication/login', $headers);

        if (!$response->token) {
            throw new Exception($response->message);
        }

        return $response->token;
    }

    /*
     * @return string the API key used for requests
     */
    private function getApiKey()
    {
        return $this->apiKey;
    }

    /*
     * @return string the client_id used for requests
     */
    private function getClientId()
    {
        return $this->clientId;
    }

    public function getPaymentIntent($id)
    {
        return $this->request('GET', '/pa/payment_intents', ['id' => $id]);
    }

    public function createPaymentIntent($params = [])
    {
        //$params parameters to create intent

        // $params=[
        //     'request_id' => uniqid(),
        //     'amount' => '0.1',
        //     'currency' => 'HKD',
        //     'merchant_order_id' => '00001'
        // ];
        return $this->request('POST', '/pa/payment_intents/create', $params);
    }

    public function confirmPaymentIntent($params = [],$id)
    {
        $url = '/pa/payment_intents/'.$id.'/confirm';
        return $this->request('POST',$url,$params);
    }
}
