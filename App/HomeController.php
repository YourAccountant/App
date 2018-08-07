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
        $test = $builder->raw("select * from users where email = ?", ['test@test'])->exec()->fetch();
        print_r($test);
    }

}
