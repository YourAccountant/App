<?php

namespace Core\Support;

class Obj
{
    public static function toArray($obj)
    {
        return json_decode(json_encode($obj), true);
    }
}
