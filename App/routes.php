<?php

use \Core\Router\Router;
use \Core\Database\Migration\Migration;


Router::get("/migration", function ($req, $res) {
    $users = Migration::table("users");
    $users->add('id')->id();
    $users->add('name')->string();
    $users->add('email')->string()->unique()->index();
    $users->add('is_active')->bool();
    $users->add('created_at')->dateCreate();
    $users->add('updated_at')->dateUpdate();


    Migration::create();
});
