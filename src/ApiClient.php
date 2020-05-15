<?php

namespace CaptchaSolver;

use Curl\Client;

abstract class ApiClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }
}