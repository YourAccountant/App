<?php

namespace App\Auth;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;
use \App\Client\Client;
use \App\Auth\OAuth\OAuthToken;

class AuthController extends Controller
{
    public function checkLoggedIn(Request $req, Response $res)
    {
        $isLoggedIn = $this->getService('AuthService.isLoggedIn');
        return $res->json(['result' => $isLoggedIn ? true : false], $isLoggedIn ? 200 : 403);
    }

    public function signin(Request $req, Response $res)
    {
        $body = $req->json();

        // validate
        if (!isset($body->email) || $body->email == null) {
            return $res->json(['error' => 'missing email'], 400);
        } elseif (!isset($body->password) || $body->password == null) {
            return $res->json(['result' => false, 'error' => 'missing password'], 400);
        } elseif (!$this->getService('AuthService.checkEmailExists', $body->email)) {
            return $res->json(['result' => false, 'error' => 'email does not exists'], 400);
        }

        $jwt = $this->getService(
            'AuthService.signin',
            $body->email,
            $body->password,
            isset($body->remindMe) && $body->remindMe != null
        );

        // signin
        if (!$jwt) {
            return $res->json(['result' => false, 'error' => 'password or email was wrong'], 403);
        }

        $payload = OAuthToken::decodeToken($jwt, OAuthToken::SESSION_TOKEN);

        $res->addCookie("authorization", $jwt, $payload->expiry);

        return $res->send(['success' => true]);
    }

    public function signup(Request $req, Response $res)
    {
        $body = $req->json();

        if (!isset($body->email)) {
            return $res->json(['error' => 'missing email'], 400);
        } elseif (!isset($body->password)) {
            return $res->json(['result' => false, 'error' => 'missing password'], 400);
        } elseif ($this->getService('AuthService.checkEmailExists', $body->email)) {
            return $res->json(['result' => false, 'error' => 'email exists'], 400);
        }

        $client = new Client();
        $client->set('email', $body->email);
        $client->set('password', $body->password);
        $client->set('role', $body->role ?? null);
        $client->set('subscription', $body->subscription ?? null);

        $this->getService('AuthService.signup', $client);

        return $res->json(['result' => true], 201);
    }

    public function signout(Request $req, Response $res)
    {
        $this->getService('AuthService.signout', $req->cookie->authorization ?? null);
        $res->addCookie("authorization", null, -1);

        return $res->json(['result' => true]);
    }

    public function emailExists(Request $req, Response $res)
    {
        $exists = $this->getService('AuthService.checkEmailExists', $req->json()->email);
        return $res->json(['result' => $exists ? true : false], $exists ? 200 : 400);
    }
}
