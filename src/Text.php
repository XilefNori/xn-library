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

    public static function ignoreNonUtf8Characters($text)
    {
        $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;

        return preg_replace($regex, '$1', $text);
    }
}
