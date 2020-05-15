<?php

namespace CaptchaSolver;

final class Utils
{
    public function findSiteKey($html)
    {
        if (preg_match('/sitekey="([^"]+)/', $html, $matches)) {
            return $matches[1];
        } else if (preg_match('/fallback\?k=([^&"]+)/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }
}