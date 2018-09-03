<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class OAuthController extends Controller
{
    private $guzzle;

    public function boot()
    {
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function grant(Request $req, Response $res)
    {
        $slug = $req->params['partner'];
        $partner = new OAuthPartner();
        if (!$partner->getBy('slug', '=', $slug)) {
            // partner not found
            die;
        }

        $token = new OAuthToken();
        $tokenId = $token->create('refreshToken', $partner->get('id'), $this->getService('AuthService.getSignedInClientId'));
        $token->getBy('id', '=', $tokenId);
        if (!$this->getService('OAuthService.sendGrant', $partner->get('redirect_url'), $token)) {
            // failed to create grant
            die;
        }

        if (isset($req->queryParameters['success'])) {
            return $res->redirect(urldecode($req->queryParameters['success']));
        }

        // redirect granted page
    }

    public function authorize(Request $req, Response $res)
    {
        $refreshToken = $req->queryParameters['refresh_token'] ?? null;

        if ($refreshToken == null) {
            return $res->send(['error' => 'Not authorized'], 402);
        }

        $token = new OAuthToken();
        $token->getByRefreshToken($refreshToken);

        if ($token->poolIsEmpty()) {
            return $res->send(['error' => 'token does not exist'], 400);
        }

        $tokenId = $token->create('bearer', $token->get('partner_id'), $token->get('client_id'));
        $bearerToken = new OAuthToken();
        $bearerToken->getBy('id', '=', $tokenId);
        return $res->send(['bearer' => $bearerToken->get('token'), 'expiry_date' => $bearerToken->get('date_expiration')]);
    }

    public function refresh(Request $req, Response $res)
    {
        $refreshToken = $req->queryParameters['refresh_token'] ?? null;

        if ($refreshToken == null) {
            return $res->send(['error' => 'Not authorized'], 402);
        }

        $token = new OAuthToken();
        $token->getByRefreshToken($refreshToken);

        if ($token->poolIsEmpty()) {
            return $res->send(['error' => 'token does not exist'], 400);
        }

        $token->refresh($token->get('id'));
    }
}
