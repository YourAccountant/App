<?php

namespace App\Auth;

use \Core\Foundation\Service;
use \App\Client\Client;
use \App\Auth\OAuth\OAuthToken;
use \App\Auth\Session;

class AuthService extends Service
{
    public function signin($email, $password, $force = false)
    {
        $client = new Client();
        $client->getBy('email', '=', $email);

        // check exists
        if ($client->poolIsEmpty()) {
            return false;
        }

        // verify pass
        if (!$force && !$this->verifyHash($client->get('password'), $password)) {
            return false;
        }

        return $this->getService("OAuthService.createSessionToken", $client->get('id'));
    }

    public function signout($authorization = null)
    {
        if ($authorization == null) {
            return true;
        }

        $session = new Session();
        $session->getBy("authorization", "=", $authorization);

        if ($session->get() != null) {
            $session->delete($session->get('id'));
        }

        return true;
    }

    public function checkEmailExists($email)
    {
        $client = new Client();
        return $client->exists('email', '=', $email);
    }

    public function signup(Client $client)
    {
        return $client->insert([
            'email' => $client->get('email'),
            'password' => $this->hashPassword($client->get('password')),
            'subscription' => $client->get('subscription') ?? $client->getDefault('subscription'),
            'role' => $client->get('role') ?? $client->getDefault('role')
        ]);
    }

    public function isLoggedIn()
    {
        return $this->getClientId() != null;
    }

    public function getClientId()
    {
        return $this->getAuthClient()->get('id') ?? null;
    }

    public function getAuthClient()
    {
        return $this->getApp()->getModel('AuthClient') ?? null;
    }

    public function setAuthClient($client)
    {
        $this->getApp()->addModel('AuthClient', $client);
        return $client;
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
