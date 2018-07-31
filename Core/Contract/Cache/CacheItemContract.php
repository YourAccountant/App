<?php

namespace Core\Contract\Cache;

interface CacheItemContract
{
    public function get();

    public function getKey();

    public function isHit();

    public function set($value);

    public function expiresAt($expiration);

    public function expiresAfter($time);
}
