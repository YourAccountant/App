<?php

namespace App;

class HomeController extends \Core\Foundation\Controller
{

    public function show($req, $res)
    {
        $res->send("Hello!");
    }

    public function test($req, $res)
    {
        $builder = $this->app->dependencies->get('Connection')->builder("users");
    }

}
