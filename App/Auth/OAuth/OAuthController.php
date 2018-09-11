<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class OAuthController extends Controller
{
    public function grant(Request $req, Response $res)
    {
        $slug = $req->params->partner;

        $partner = new OAuthPartner();
        $partner->getBy('slug', '=', $slug);

        if (empty($partner->get())) {
            return $res->send(" <h1>Partner is not in our system</h1> ");
        }
    }

    public function authorize(Request $req, Response $res)
    {
        $refreshToken = $req->json()->refresh_token;

        if ($refreshToken == null) {
            return $res->send(['error' => 'Not authorized'], 402);
        }

        $token = new OAuthToken();
        $token->getByRefreshToken($refreshToken);

        if ($token->poolIsEmpty()) {
            return $res->send(['error' => 'token does not exist'], 400);
        }

        $tokenId = $token->create('bearer', $token->get('client_id'), $token->get('oauth_partner_id'));

        $bearerToken = new OAuthToken();
        $bearerToken->getBy('id', '=', $tokenId);
        return $res->send(['action' => 'authorize', 'token' => $bearerToken->get('token'), 'expiry_date' => $bearerToken->get('expiry')]);
    }

    public function refresh(Request $req, Response $res)
    {
        $payload = $this->getService("OAuthService.refreshAccessTokeToken", $req->json()->refresh_token);

        $res->send([
            'action' => 'refresh',
            'token_type' => $payload['token_type'],
            'token' => $payload['access_token'],
            'expiry' => $payload['expiry']
        ], 200);
    }
}
