<?php

namespace CaptchaSolver\TwoCaptcha;

use CaptchaSolver\ApiClient;

class Client extends ApiClient
{
    protected $api_key = '';
    protected $proxy = false;

    public function __construct($options)
    {
        parent::__construct();

        if (is_string($options)) {
            $this->api_key = $options;
        } else if (is_array($options)) {
            $this->api_key = array_key_exists('key', $options) ? $options['key'] : null;
            $this->proxy = array_key_exists('proxy', $options) ? $options['proxy'] : null;
        }
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getBalance()
    {
        $response = $this->client->get('https://2captcha.com/res.php', [
            'key' => $this->api_key,
            'action' => 'getbalance'
        ]);

        // ERROR_WRONG_USER_KEY
        // ERROR_EMPTY_ACTION
        if (is_numeric($response->body)) {
            return $response->body;
        }

        return null;
    }

    public function send(InRequest $params)
    {
        $params->key = $this->api_key;
        $params->json = 1;

        if (empty($params->method)) {
            $params->method = 'userrecaptcha';
        }

        // Are we using a proxy?
        if ($this->proxy) {
            $params->proxy = $this->proxy;
            $params->proxytype = 'HTTP';
        }

        $response = $this->client->post('http://2captcha.com/in.php', $params->toArray());
        return new InResponse($response);
    }

    public function getResult($request_id)
    {
        $response = $this->client->get('https://2captcha.com/res.php', [
            'key' => $this->api_key,
            'action' => 'get',
            'json' => 1,
            'id' => $request_id
        ]);

        return new ResResponse($response);
    }

    public function solveReCaptchaV2(InRequest $params, $timeout = 90)
    {
        $response = $this->send($params);
        $request_id = $response->getResult();

        // Something went wrong! Maybe proxy we are using went offline?
        if (empty($request_id)) {
            return null;
        }

        $solution = null;
        $time_left = $timeout;

        // If captcha is not solved yet server will return CAPCHA_NOT_READY result. Repeat your request in 5 seconds.
        $sleep_interval = 5;

        do {

            sleep($sleep_interval);
            $solution_response = $this->getResult($request_id);

            $solution = $solution_response->getSolution();

            $time_left -= $sleep_interval;

        } while ($solution == false && $time_left > 0);

        return $solution;
    }
}