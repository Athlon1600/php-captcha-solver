<?php

namespace CaptchaSolver\TwoCaptcha;

class InRequest
{
    public $key; // required
    public $method;
    public $googlekey; // required
    public $pageurl; // required
    public $invisible;
    public $data_s;
    public $cookies;
    public $userAgent;
    public $header_acao;
    public $pingback;
    public $json;
    public $soft_id;
    public $proxy;
    public $proxytype;

    public function __construct($array = [])
    {
        foreach ($array as $key => $value) {

            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $vars = get_object_vars($this);

        $values = array_filter($vars, function ($val) {
            return !is_null($val);
        });

        // has to be dash...
        if (array_key_exists('data_s', $values)) {
            $values['data-s'] = $values['data_s'];
            unset($values['data_s']);
        }

        return $values;
    }
}