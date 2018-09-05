<?php

namespace App\Auth;

use \Core\Foundation\Service;
use \App\Client\Client;
use \App\Auth\OAuth\OAuthToken;

class AuthService extends Service
{
    public function isLoggedIn()
    {
        return $this->getSignedinClientId() != null;
    }

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

        // create session token and save client
        $token = new OAuthToken();
        $tokenId = $token->create('bearer', $client->get('id'));
        $token->getBy('id', '=', $tokenId);

        $client->set('token', $token->get('token'));
        $client->set('expiry', $token->get('expiry'));

        $this->setAuthClient($client);

        return true;
    }

    public function signout()
    {
        unset($_SESSION['auth']);
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

    public function getSignedinClientId()
    {
        return $this->getAuthClient()->get('id');
    }

    public function getAuthClient()
    {
        return $this->getApp()->getModel('AuthClient');
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
