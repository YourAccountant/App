<?php

namespace App\Util;

use \DateTime;
use \Exception;

class DateUtil {
    public static function isValid($date)
    {
        try {
            new DateTime($date);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
