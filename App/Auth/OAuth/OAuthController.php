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
        $refreshToken = $req->json()->refreshToken;

        if ($refreshToken == null) {
            return $res->json(['error' => 'Not authorized'], 401);
        }

        $token = new OAuthToken();
        $token->getByRefreshToken($refreshToken);

        if ($token->poolIsEmpty()) {
            return $res->json(['error' => 'token does not exist'], 400);
        }

        $tokenId = $token->create('bearer', $token->get('clientId'), $token->get('oauthPartnerId'));

        $bearerToken = new OAuthToken();
        $bearerToken->getBy('id', '=', $tokenId);
        return $res->json(['action' => 'authorize', 'token' => $bearerToken->get('token'), 'expiryDate' => $bearerToken->get('expiry')]);
    }

    public function refresh(Request $req, Response $res)
    {
        $payload = $this->getService("OAuthService.refreshAccessTokeToken", $req->json()->refreshToken);

        $res->json([
            'action' => 'refresh',
            'tokenType' => $payload['tokenType'],
            'token' => $payload['accessToken'],
            'expiry' => $payload['expiry']
        ], 200);
    }
}
