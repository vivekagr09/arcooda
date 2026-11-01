<?php

namespace app\models\Airwallex\Service;

/*
 * This is the main service container factory.
 * it instantiates the service object based on the class key in $classMap array.
 */

use app\models\Airwallex\Service\PaymentIntentService;

class CoreServiceFactory
{
    private $client;

    private $services;

    private static $classMap = [
        'paymentIntent' => PaymentIntentService::class,
    ];

    public function __construct($client)
    {
        $this->client   = $client;
        $this->services = [];
    }

    protected function getServiceClass($name)
    {
        return array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }

    public function __get($name)
    {
        $serviceClass = $this->getServiceClass($name);
        if (null !== $serviceClass) {
            if (!array_key_exists($name, $this->services)) {
                $this->services[$name] = new $serviceClass($this->client);
            }

            return $this->services[$name];
        }

        return null;
    }
}
