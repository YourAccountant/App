<?php

namespace Core\Support;

class Arr
{
    public static function toObject($arr)
    {
        return json_decode(json_encode($arr));
    }
}
