<?php

namespace Core\Support;

class Str
{
    public static function camelCaseToSnakeCase($str)
    {
        $result = '';
        foreach (str_split($str) as $key => $value) {
            if ($key > 0 && ctype_upper($value)) {
                $result .= "_";
            }

            $result .= $value;
        }

        return strtolower($result);
    }

    public static function getRandomString($length = 24, $chars = 'qwertyuiopasdfghjklzxcvbnm1234567890')
    {
        $chars = is_array($chars) ? $chars : str_split($chars);

        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, count($chars) - 1)];
        }

        return $str;
    }
}
