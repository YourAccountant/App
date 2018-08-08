<?php

namespace App;

class HomeController extends \Core\Foundation\Controller
{

    public function show($req, $res)
    {
        $res->send("<h1>Home</h1>");
    }

}
