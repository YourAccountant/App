<?php

namespace Core\Foundation;

use \Core\Support\Arr;

class Controller extends Bootable
{
    public function isset($body, $key)
    {
        return Arr::isset($body, $key);
    }
}
