<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;
use \App\Client\Client;

class OAuthPolicy extends Policy
{
    public $error_codes;

    public function boot() {
        $this->codes = new \stdClass();
        $this->codes->not_authorized = 1000;
        $this->codes->not_found = 1001;
        $this->codes->expired = 1002;
    }

    public function checkBearer(Request $req, Response $res)
    {
        if (Request::isAjax()) {
            $bearer = $_COOKIE['token'] ?? null;
        } else {
            $bearer = $req->headers->authorization ?? null;
        }

        if ($bearer == null) {
            return $res->send([
                'error' => 'not authorized',
                'code' => $this->codes->not_authorized
            ], 402);
        }

        $bearer = str_replace('Bearer ', '', $bearer);

        $token = new OAuthToken();
        $token->getByBearer($bearer);

        if ($token->poolIsEmpty()) {
            return $res->send([
                'error' => 'token not found',
                'code' => $this->codes->not_found
            ], 403);
        }

        if (!$token->checkExpiry()) {
            if (Request::isAjax()) {
                $token->refresh($token->get('id'));

                $res->addCookie("token", $token->get('token'), $token->get('expiry'));

                return $res->send([
                    'error' => 'token expired',
                    'code' => $this->codes->expired,
                ]);
            }

            return $res->send([
                'error' => 'token expired',
                'code' => $this->codes->expired
            ], 400);
        }

        $client = new Client();
        $client->getBy('id', '=', $token->get('client_id'));
        $client->set('token', $token->get('token'));
        $this->getService('AuthService.setAuthClient', $client);

        $req->set('token', $token);
    }
}
