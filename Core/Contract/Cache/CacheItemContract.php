<?php

namespace Core\Contract\Cache;

interface CacheItemContract
{
    public function __construct(string $key, $content);

    public function get();

    public function getKey();
}
