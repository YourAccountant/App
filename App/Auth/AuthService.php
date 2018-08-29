<?php

namespace App\Auth;

use \Core\Foundation\Service;
use \App\Client\Client;

class AuthService extends Service
{
    public function isLoggedIn()
    {
        if (!isset($_SESSION['auth']['isLoggedIn'])) {
            $_SESSION['auth']['isLoggedIn'] = false;
        }

        return $_SESSION['auth']['isLoggedIn'] ? true : false;
    }

    public function signin($email, $password)
    {
        $client = $this->getDependencies()
            ->get('Connection')
            ->builder('clients')
            ->columns("`id`, `password`")
            ->where('email', '=', $email)
            ->limit(1)
            ->exec()
            ->fetch();

        if (!$this->verifyHash($client->password, $password)) {
            return false;
        }

        $_SESSION['auth']['isLoggedIn'] = true;
        $_SESSION['auth']['client'] = $client->id;
        return true;
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

    public function signup(Client $client)
    {
        $this->getDependencies()
            ->get('Connection')
            ->builder('clients')
            ->insert(['email' => $client->email, 'password' => $this->hashPassword($client->password)])
            ->exec();
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyHash($hash, $password)
    {
        return \password_verify($password, $hash);
    }
}
