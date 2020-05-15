<?php

namespace CaptchaSolver;

class TwoCaptcha extends ApiClient
{
    protected $api_key = '';
    protected $proxy = false;

    public function __construct($options)
    {
        parent::__construct();

        // required, but will fail silently
        $this->api_key = array_key_exists('key', $options) ? $options['key'] : null;
        $this->proxy = array_key_exists('proxy', $options) ? $options['proxy'] : null;
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

    public function sendReCaptchaV2($site_key, $page_url)
    {
        $request_data = array(
            'key' => $this->api_key,
            'method' => 'userrecaptcha',
            'json' => 1,
            'pageurl' => $page_url,
            'googlekey' => $site_key
        );

        // Are we using a proxy?
        if ($this->proxy) {
            $request_data['proxy'] = $this->proxy;
            $request_data['proxy_type'] = 'HTTP';
        }

        $response = $this->client->post('http://2captcha.com/in.php', $request_data);
        $json = json_decode($response->body, true);

        return $json ? $json['request'] : null;
    }

    public function getReCaptchaV2($request_id)
    {
        $response = $this->client->get('https://2captcha.com/res.php', [
            'key' => $this->api_key,
            'action' => 'get',
            'json' => 1,
            'id' => $request_id
        ]);

        $json = json_decode($response->body, true);

        if (isset($json['request']) && $json['request'] != 'CAPCHA_NOT_READY') {
            return $json['request'];
        }

        return false;
    }

    public function solveReCaptchaV2($site_key, $page_url, $timeout = 90)
    {
        $request_id = $this->sendReCaptchaV2($site_key, $page_url);

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
            $solution = $this->getReCaptchaV2($request_id);

            $time_left -= $sleep_interval;

        } while ($solution == false && $time_left > 0);

        return $solution;
    }
}