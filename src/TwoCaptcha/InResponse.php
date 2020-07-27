<?php

namespace CaptchaSolver\TwoCaptcha;

class InResponse extends Response
{
    public function getResult()
    {
        if ($this->hasError() == false && array_key_exists('request', $this->json)) {
            return $this->json['request'];
        }

        return null;
    }
}