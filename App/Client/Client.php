<?php

namespace App\Client;

use \Core\Foundation\Model;

class Client extends Model
{
    protected $table = "clients";

    protected $ignore = ['password'];

    public function get($clientId)
    {
        return $this->getBy('id', '=', $clientId);
    }
}
