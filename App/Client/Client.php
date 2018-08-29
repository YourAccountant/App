<?php

namespace App\Client;

use \Core\Foundation\Model;

class Client extends Model
{
    public $id;

    public $email;

    public $password;

    protected $table = "clients";
}
