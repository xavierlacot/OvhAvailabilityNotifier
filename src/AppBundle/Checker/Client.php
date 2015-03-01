<?php

namespace AppBundle\Checker;

use GuzzleHttp\Client as BaseClient;

class Client extends BaseClient
{
    protected static $endpoint = 'http://ws.ovh.com/dedicated/r2/ws.dispatcher/getAvailability2';

    public function getAvailabilities()
    {
        $response = $this->get(self::$endpoint);
        $content = $response->json();

        return $content['answer']['availability'];
    }
}
