<?php

namespace Core\Foundation;

abstract class Command extends Bootable
{

    abstract public function run($data = null);
}
