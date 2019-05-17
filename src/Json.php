<?php

namespace XnLibrary;

class Json
{
    public static function format($data, $utf8 = false, $indent = false, $slashes = false)
    {
        $options = 0;

        $utf8 and $options |= JSON_UNESCAPED_UNICODE;
        $indent and $options |= JSON_PRETTY_PRINT;
        $slashes and $options |= JSON_UNESCAPED_SLASHES;

        return json_encode($data, $options);
    }

    public static function saveToFile($data, $filename, $utf8 = false)
    {
        file_put_contents($filename, self::format($data, $utf8, true));
    }
}
