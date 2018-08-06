<?php

namespace Core\Contract\Foundation;

use \Core\Foundation\Application;

interface DependsOnApp
{
    public function setApp(Application $app);
}
