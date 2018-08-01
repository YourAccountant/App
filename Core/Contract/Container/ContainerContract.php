<?php

namespace Core\Contract\Container;

interface ContainerContract
{
    public function get($key);

    public function has($key);

    public function delete($key);
}
