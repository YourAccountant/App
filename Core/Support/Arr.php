<?php

namespace Core\Support;

class Arr
{
    public static function toObject($arr)
    {
        return json_decode(json_encode($arr));
    }

    public static function isAssoc($arr)
    {
        if ([] === $arr) {
            return false;
        }

        ksort($arr);
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function toArray($obj)
    {
        return json_decode(json_encode($obj), true);
    }
}
