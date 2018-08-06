<?php

use \Core\Router\Router;
use \Core\Database\Migration\Migration;


Router::get("/migration", function ($req, $res) {
    $m = new Migration('users');
    $m->add("id")->id();
    $m->add("email")->string()->index()->unique();
    $m->add("bio")->text()->nullable();
    $m->add("is_active")->bool();
    $m->add("date_create")->dateCreate();
    $m->add("date_update")->dateUpdate();

    echo '<pre style="">';
    print_r($m->getSql());
    echo '</pre>';
});
