<?php

namespace App\Auth;

use \Core\Foundation\Service;

class AuthService extends Service
{
    public function isLoggedIn()
    {
        if (!isset($_SESSION['auth']['isLoggedIn'])) {
            $_SESSION['auth']['isLoggedIn'] = false;
        }

        return $_SESSION['auth']['isLoggedIn'];
    }
}
