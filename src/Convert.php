<?php

namespace XnLibrary;

class Convert
{
    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }


    public static function base64EncodeImage($filename)
    {
        list(, , $type) = getimagesize($filename);
        $mime = image_type_to_mime_type($type);

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($filename));
    }
}
