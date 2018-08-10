<?php

namespace App;

class HomeController extends \Core\Foundation\Controller
{

    public function show($req, $res)
    {
        $res->view("home.php", "layout.php", ['title' => $this->getDependencies()->get('Config')->name]);
    }

}
