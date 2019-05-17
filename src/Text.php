<?php

namespace XnLibrary;

class Text
{
    public static function underscoreToCamelCase($name)
    {
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return $name;
    }

    public static function camelCaseToUnderscore($name)
    {
        $name = ltrim(preg_replace('/[A-Z]/', '_$0', $name), '_');

        return strtolower($name);
    }


    public static function mbGetStringBetween($string, $start, $end)
    {
        if (false === ($ini = mb_strpos($string, $start))) {
            return '';
        }

        $ini += mb_strlen($start);
        $len = mb_strpos($string, $end, $ini) - $ini;

        return mb_substr($string, $ini, $len);
    }

    public static function incrementNumInString($string, $incBy = 1, $regex = '\d+')
    {
        return preg_replace_callback("~$regex~", function ($m) use ($incBy) {
            return ((int) $m[0]) + $incBy;
        }, $string);
    }
}
