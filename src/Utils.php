<?php

namespace CaptchaSolver;

class Utils
{
    public static function findSiteKey($html)
    {
        if (preg_match('/sitekey="([^"]+)/', $html, $matches)) {
            return $matches[1];
        } else if (preg_match('/fallback\?k=([^&"]+)/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function findDataSVariable($html)
    {
        if (preg_match('/data-s="([^"]+)/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    // <input name="NAME" value="VALUE"
    public static function getInputValueByName($html, $name)
    {
        if (preg_match("/name=(['\"]){$name}\\1[^>]+value=(['\"])(.*?)\\2/is", $html, $matches)) {
            return $matches[3];
        }

        return null;
    }
}
