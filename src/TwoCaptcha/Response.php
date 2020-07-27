<?php

namespace CaptchaSolver\TwoCaptcha;

use CaptchaSolver\JsonResponse;

class Response extends JsonResponse
{
    public function getStatus()
    {
        return array_key_exists('status', $this->json) ? $this->json['status'] : null;
    }

    public function getError()
    {
        if (parent::getError()) {
            return parent::getError();
        }

        if (isset($this->json['error_text'])) {
            return $this->json['error_text'];
        } else {

            if (isset($this->json['request'])) {
                $request = $this->json['request'];

                if (strpos($request, 'ERROR_') === 0) {
                    return $request;
                }
            }
        }

        return null;
    }

    public function hasError()
    {
        return !empty($this->getError());
    }
}