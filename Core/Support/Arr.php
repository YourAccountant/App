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

    public static function issetArr($body, $keys)
    {
        $result = true;
        foreach($keys as $key) {
            if (!self::isset($body, $key)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    public static function isset($body, $key)
    {
        if (is_array($key)) {
            return self::issetArr($body, $key);
        }

        if (is_array($body)) {
            return isset($body[$key]) && $body[$key] != null;
        } elseif (is_object($body)) {
            return isset($body->$key) && $body->$key != null;
        }

        return false;
    }

    public static function push($arr, $key, $value)
    {
        if (is_object($arr)) {
            $arr->$key = $value;
        } elseif (is_array($arr)) {
            $arr[$key] = $value;
        }

        return $arr;
    }

    public static function addIfSet($result, $body, $keys)
    {
        foreach ($keys as $key) {
            if (is_array($body)) {
                if (isset($body[$key])) {
                    $result = self::push($result, $key, $body[$key]);
                }
            } else {
                if (isset($body->$key)) {
                    $result = self::push($result, $key, $body->$key);
                }
            }
        }

        return $result;
    }
}
