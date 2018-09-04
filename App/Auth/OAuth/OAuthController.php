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
        if(!$this->getService('AuthService.signin', "k@h.nl", "123")) {
            $this->printLog();
        }

        $slug = $req->params['partner'];
        $partner = new OAuthPartner();

        if (!$partner->getBy('slug', '=', $slug)) {
            return $res->send(" <h1>Partner is not in our system</h1> ");
        }

        $token = new OAuthToken();
        $tokenId = $token->create('refresh_token', $partner->get('id'), $this->getService('AuthService.getSignedInClientId'));
        $token->getBy('id', '=', $tokenId);

        if (!$this->getService('OAuthService.sendGrant', $partner->get('redirect_url'), $token)) {
            return $res->send(" <h1>Failed to grant permission</h1> ");
        }

        if (isset($req->queryParameters['success'])) {
            return $res->redirect(urldecode($req->queryParameters['success']));
        }

        // redirect granted page
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

        $tokenId = $token->create('bearer', $token->get('oauth_partner_id'), $token->get('client_id'));

        $bearerToken = new OAuthToken();
        $bearerToken->getBy('id', '=', $tokenId);
        return $res->send(['action' => 'authorize', 'token' => $bearerToken->get('token'), 'expiry_date' => $bearerToken->get('date_expiration')]);
    }

    public function refresh(Request $req, Response $res)
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

        $token->refresh($token->get('id'));
        return $res->send(['action' => 'refresh', 'token' => $token->get('token'), 'expiry_date' => $token->get('date_expiration')]);
    }
}
