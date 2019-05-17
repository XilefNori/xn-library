<?php

namespace XnLibrary;

abstract class ExceptionHelper
{
    public static $projectRoot;

    public static function info(\Exception $e, $show = 'fp')
    {
        $class    = get_class($e);
        $code     = $e->getCode();
        $line     = $e->getLine();
        $file     = $e->getFile();
        $msg      = strip_tags($e->getMessage());
        $previous = $e->getPrevious();

        if (self::$projectRoot) {
            $file = str_replace(self::$projectRoot, '', $file);
        }

        // Показывать имя файла
        if (false !== strpos($show, 'f')) {
            $info = sprintf("%s(%s) at [%s:%d]: '%s'", $class, $code, $file, $line, $msg);
        } else {
            $info = sprintf("%s(%s): '%s'", $class, $code, $msg);
        }

        // Показывать стек вызова (c параметрами или без них)
        if (false !== strpos($show, 's')) {
            $info = $info . "\n" . "\n" . self::traceText($e, false !== strpos($show, 'a')) . "\n";
        }

        // Показывать предыдущее исключение
        if ($previous && false !== strpos($show, 'p')) {
            $info = $info . "\n" . self::info($previous, $show);
        }

        return $info;
    }

    public static function traceText(\Exception $e, $show_args = false)
    {
        $root = self::$projectRoot;

        $text = '';

        foreach ($e->getTrace() as $key => $i) {
            $class    = $i['class'] ?? '';
            $type     = $i['type'] ?? '';
            $function = $i['function'] ?? '';
            $file     = $i['file'] ?? '';
            $line     = $i['line'] ?? '';

            if (self::$projectRoot) {
                $file = str_replace($root, '', $file);
            }


            $method = $class . $type . $function;
            $addr   = $file . ':' . $line;

            $addr = ':' == $addr ? '<unknown>' : $addr;

            $text .= sprintf("[%2d] %-40s %s\n", $key, $method, $addr);
            if ($show_args && isset($i['args']) && $i['args']) {
                $text .= $i['function'] . ' ' . json_encode($i['args']) . "\n";
            }
        }

        return $text;
    }
}
