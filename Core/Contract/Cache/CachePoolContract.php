<?php

namespace Core\Contract\Cache;

interface CachePoolContract
{
    public function getItem($key);

    public function getItems($keys = []);

    public function hasItem($key);

    public function clear();

    public function deleteItem($key);

    public function deleteItems($keys = []);

    public function save(CacheItemContract $item);

    public function saveDeferred(CacheItemContract $item);

    public function commit();
}
