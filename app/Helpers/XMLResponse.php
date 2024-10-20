<?php

namespace App\Helpers;

class XMLResponse
{
    public static function make($data, $rootElement = 'Response', $xml = null)
    {
        if ($xml === null) {
            $xml = new \SimpleXMLElement("<$rootElement/>");
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                self::make($value, $key, $xml->addChild($key));
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }

        return $xml->asXML();
    }
}
