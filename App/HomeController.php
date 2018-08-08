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
        print_r($this);
    }

    public function test2($req, $res)
    {
        echo "test";
    }

}
