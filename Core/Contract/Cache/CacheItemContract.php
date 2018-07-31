<?php

namespace Core\Contract\Cache;

interface CacheItemContract
{
    public function get();

    public function getKey();

    public function set(string $key, $value);
}
