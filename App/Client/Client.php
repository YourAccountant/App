<?php

namespace App\Client;

use \Core\Foundation\Model;

class Client extends Model
{
    protected $table = "clients";

    protected $roles = ['client', 'partner', 'admin'];

    protected $subscriptions = ['none', 'paid', 'demo'];

    protected $ignore = ['password'];

    public function getDefault($type)
    {
        if ($type == 'subscription') {
            return 'none';
        } elseif ($type == 'role') {
            return 'client';
        }
    }
}
