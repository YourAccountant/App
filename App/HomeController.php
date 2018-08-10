<?php

namespace App;

class HomeController extends \Core\Foundation\Controller
{

    public function show($req, $res)
    {
        $user= $this->getDependencies()
            ->get('Connection')
            ->builder('users')
            ->get(1)
            ->fetch();

        $res->view("home.php", "layout.php", [
            'title' => $this->getDependencies()->get('Config')->name,
            'user' => $user
        ]);
    }
}
