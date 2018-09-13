<?php

namespace Core\Foundation;

class Controller extends Bootable
{
    public function isset($body, $key)
    {
        if (is_array($body)) {
            return isset($body[$key]) && $body[$key] != null;
        } elseif (is_object($body)) {
            return isset($body->$key) && $body->$key != null;
        }

        return false;
    }
}
