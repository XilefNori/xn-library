<?php

namespace XnLibrary;

class ArrayLib
{
    public static function sortByKeyOrValue($array)
    {
        if (is_numeric(key($array))) {
            sort($array);
        } else {
            ksort($array);
        }

        foreach ($array as &$v) {
            if (is_array($v)) {
                $v = self::sortByKeyOrValue($v);
            }
        }
        unset($v);

        return $array;
    }

    /**
     * @param \Iterator $iterator
     * @param callable $callback
     * @param int      $chunk_size
     */
    public static function processByChunks($iterator, $callback, $chunk_size)
    {
        $chunk = [];
        foreach ($iterator as $item) {
            $chunk[] = $item;

            if (count($chunk) >= $chunk_size) {
                $callback($chunk);

                $chunk = [];
            }
        }

        if ($chunk) {
            $callback($chunk);
        }
    }

    /**
     * Сравнить две переменных списочного типа по заданному порядку
     *
     * @param mixed   $a
     * @param mixed   $b
     * @param mixed[] $order Порядок значений (что чего больше)
     *
     * @return mixed
     */
    public static function cmpByList($a, $b, $order)
    {
        $fliped = array_flip(array_values($order));

        return $fliped[$a] - $fliped[$b];
    }

}
