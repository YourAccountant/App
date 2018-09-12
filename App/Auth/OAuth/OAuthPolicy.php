<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;
use \App\Client\Client;
use \App\Auth\Session;

class OAuthPolicy extends Policy
{
    public $codes;

    public function boot()
    {
        $this->codes = new \stdClass();
        $this->codes->not_authorized = 1000;
        $this->codes->not_found = 1001;
        $this->codes->expired = 1002;
        $this->codes->invalid = 1003;
    }

    public function authorize(Request $req, Response $res)
    {
        $bearer = $req->headers->authorization ?? $_COOKIE['authorization'] ?? null;

        // check bearer exists
        if ($bearer == null) {
            return $res->send([
                "error" => "not authorized",
                "code" => $this->codes->not_authorized
            ], 401);
        }

        $type = isset($_COOKIE['authorization']) ? OAuthToken::SESSION_TOKEN : OAuthToken::ACCESS_TOKEN;

        // decode jwt
        $payload = OAuthToken::decodeToken($bearer, $type);

        // check required fields payload for both session and access tokens
        if ($payload == null) {
            return $res->send([
                "error" => "invalid token",
                "code" => $this->codes->invalid
            ], 401);
        }

        // check token expired
        if (!OAuthToken::checkExpiry($payload->expiry)) {
            return $res->send([
                "error" => "token expired",
                "code" => $this->codes->expired
            ], 400);
        }

        // set session client
        $client = new Client();
        $client->set('id', $payload->client_id);

        $this->getService("AuthService.setAuthClient", $client);
    }
}
