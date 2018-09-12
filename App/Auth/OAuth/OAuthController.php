<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class OAuthController extends Controller
{
    public function createPartner(Request $req, Response $res)
    {
        $data = $req->json();

        if (!isset($data->name) || $data->name == null) {
            return $res->json([
                "error" => "missing name"
            ], 400);
        }

        $partner = new OAuthPartner();
        if ($partner->exists("name", "=", $data->name)) {
            return $res->json([
                "error" => "name already exists"
            ]);
        }

        $id = $partner->create($this->getService('AuthService.getClientId'), [
            "name" => $data->name,
            "desc" => $data->desc ?? ""
        ]);

        return $res->json(["success" => true], 201);
    }

    public function grant(Request $req, Response $res)
    {
        $slug = $req->params->partner;

        $partner = new OAuthPartner();
        $partner->getBy('slug', '=', $slug);

        if (empty($partner->get())) {
            return $res->json(" <h1>Partner is not in our system</h1> ");
        }
    }

    public function authorize(Request $req, Response $res)
    {
        $refreshToken = $req->json()->refresh_token;

        if ($refreshToken == null) {
            return $res->json(['error' => 'Not authorized'], 401);
        }

        $token = new OAuthToken();
        $token->getByRefreshToken($refreshToken);

        if ($token->poolIsEmpty()) {
            return $res->json(['error' => 'token does not exist'], 400);
        }

        $tokenId = $token->create('bearer', $token->get('client_id'), $token->get('oauth_partner_id'));

        $bearerToken = new OAuthToken();
        $bearerToken->getBy('id', '=', $tokenId);
        return $res->json(['action' => 'authorize', 'token' => $bearerToken->get('token'), 'expiry_date' => $bearerToken->get('expiry')]);
    }

    public function refresh(Request $req, Response $res)
    {
        $payload = $this->getService("OAuthService.refreshAccessTokeToken", $req->json()->refresh_token);

        $res->json([
            'action' => 'refresh',
            'token_type' => $payload['token_type'],
            'token' => $payload['access_token'],
            'expiry' => $payload['expiry']
        ], 200);
    }
}
