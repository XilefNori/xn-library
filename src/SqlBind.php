<?php

namespace XnLibrary;

class SqlBind
{
    public static function getStrBindsByParams(array $queryParams): string
    {
        $keys = array_fill(0, count($queryParams), '?');

        return implode(', ', $keys);
    }

    public static function getArrayBindsByParams(array $data_arr, $prefix): array
    {
        $values = array();
        $i      = 1;
        foreach ($data_arr as $val) {
            $values[$prefix . '_' . $i] = $val;
            $i++;
        }

        return $values;
    }
}
