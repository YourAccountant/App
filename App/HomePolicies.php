<?php

namespace App;

class HomePolicies extends \Core\Foundation\Policy
{

    public function isAllowed($req, $res)
    {
        $config = $this->app->dependencies->get("Config");
        echo $config->name;

    }

}
