<?php

namespace Core\Support;

class Str
{
    public static function camelCaseToSnakeCase($str)
    {
        $result = '';
        foreach(str_split($str) as $key => $value) {
            if ($key > 0 && ctype_upper($value)) {
                $result .= "_";
            }

            $result .= $value;
        }

        return strtolower($result);
    }
}
