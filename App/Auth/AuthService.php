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

        return $_SESSION['auth']['isLoggedIn'] ? true : false;
    }

    public function checkEmailExists($email)
    {
        $count = $this->getDependencies()
            ->get('Connection')
            ->builder('clients')
            ->columns("COUNT(*) as emails")
            ->where('email', '=', $email)
            ->limit(1)
            ->exec()
            ->fetch();

        return ($count->emails > 0);
    }

    public function checkPasswordValid($password)
    {
        # code...
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function validateHash($clientId, $password)
    {
        return \password_verify("", $password);
    }
}
