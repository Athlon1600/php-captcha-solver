<?php

namespace CaptchaSolver\TwoCaptcha;

class ResResponse extends Response
{
    public function hasSolution()
    {
        if (!$this->getError() && !$this->isNotReady()) {
            return true;
        }

        return false;
    }

    public function isNotReady()
    {
        if (isset($this->json['request'])) {
            return $this->json['request'] === 'CAPCHA_NOT_READY';
        }

        return false;
    }

    public function getSolution()
    {
        if ($this->hasSolution()) {
            return $this->json['request'];
        }

        return null;
    }
}
