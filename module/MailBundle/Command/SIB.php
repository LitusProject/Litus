<?php

namespace MailBundle\Command;

class SIB
{
    private $config;
    private $apiInstance;

    public function __construct() {
        $this->config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', 'YOUR API KEY');
        $this->apiInstance = new SendinBlue\Client\Api\AttributesApi(
            new GuzzleHttp\Client(),
            $this->config;
        );
    }
}