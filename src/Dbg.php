<?php

namespace XnLibrary;

use Closure;

class Dbg
{
    /**
     * @param callable $callable
     *
     * @return string
     */
    public static function getCallableName($callable): string
    {
        if (is_string($callable)) {
            return trim($callable);
        }

        if (is_array($callable)) {
            if (is_object($callable[0])) {
                return sprintf('%s::%s', get_class($callable[0]), trim($callable[1]));
            }

            return sprintf('%s::%s', trim($callable[0]), trim($callable[1]));
        }

        if ($callable instanceof Closure) {
            return 'closure';
        }

        return 'unknown';
    }

    public static function getType($var)
    {
        $type = gettype($var);

        return $type === 'object' ? get_class($var) : $type;
    }

    /**
     * @param array $obj
     * @param array $lengths
     */
    public static function shortenProps(array & $obj, array $lengths)
    {
        foreach ($lengths as $key => $length) {
            $obj[$key] = mb_substr($obj[$key], 0, $length);
        }
    }

    public static function logDump($r, $label = 'DEBUG')
    {
        self::logWrite($r, $label, str_replace('log_', '', __FUNCTION__));
    }

    public static function logExport($r, $label = 'DEBUG')
    {
        self::logWrite($r, $label, str_replace('log_', '', __FUNCTION__));
    }

    public static function logPrint($r, $label = 'DEBUG')
    {
        self::logWrite($r, $label, str_replace('log_', '', __FUNCTION__));
    }

    public static function logWrite($r, $label = 'DEBUG', $type = 'export')
    {
        $msg = '';

        $type = strtolower($type);

        switch ($type) {
            case 'export':
                $msg = var_export($r, true);
                break;
            case 'print':
                $msg = print_r($r, true);
                break;
            case 'dump':
                ob_start();
                ini_set('html_errors', 0);
                /** @noinspection ForgottenDebugOutputInspection */
                var_dump($r);
                ini_restore('html_errors');
                $msg = ob_get_clean();
                break;
        }

        // $msg = preg_replace('/\n/', "\n$label: ", $msg);
        $str = "$label: " . $msg;

        error_log($str);
    }
}
