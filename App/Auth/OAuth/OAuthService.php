<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Service;
use \App\Client\Client;
use \App\Auth\Session;
use \Core\Router\Request;

class OAuthService extends Service
{
    public function boot()
    {
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function sendGrant($url, OAuthToken $token)
    {
        try {
            $response = $this->guzzle->post($url, [
                'headers' => [
                    'Content-type' => 'application/json'
                ],
                'json' => [
                    'action' => 'grant',
                    'token_type' => OAuthToken::REFRESH_TOKEN,
                    'token' => $token->get('token'),
                    'reference' => $_GET['reference'] ?? null
                ]
            ]);

            if ($response->getStatusCode() > 300) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function createRefreshToken($clientId, $partnerId)
    {
        $type = OAuthToken::REFRESH_TOKEN;

        $expiry = OAuthToken::getExpiryDate($type);

        $payload = [
            'token_type' => $type,
            'client_id' => $clientId,
            'expiry' => $expiry,
            'create_date' => date('Y-m-d H:i:s'),
            'partner_id' => $partnerId
        ];

        $token = new OAuthToken();

        $jwt = $token->generateToken($payload, $type);

        $token->insert([
            'client_id' => $clientId,
            'oauth_partner_id' => $partnerId,
            'token_type' => $type,
            'token' => $jwt
        ]);

        return $jwt;
    }

    public function createSessionToken($clientId)
    {
        $type = OAuthToken::SESSION_TOKEN;
        $expiry = OAuthToken::getExpiryDate($type);

        $payload = [
            'token_type' => $type,
            'client_id' => $clientId,
            'expiry' => $expiry,
            'ip' => Request::getIp(),
            'create_date' => date('Y-m-d H:i:s')
        ];

        $token = new OAuthToken();
        $jwt = $token->generateToken($payload, $type);

        $session = new Session();

        $session->insert([
            'client_id' => $clientId,
            'ip' => Request::getIp(),
            'authorization' => $jwt,
            'expiry' => $expiry
        ]);

        return $jwt;
    }

    public function refreshAccessTokeToken($refreshToken)
    {
        $payload = OAuthToken::decodeToken($refreshToken, OAuthToken::REFRESH_TOKEN);

        $token = new OAuthToken();

        $jwtPayload = [
            'token_type' => OAuthToken::ACCESS_TOKEN,
            'token' => $refreshToken,
            'client_id' => $payload->client_id,
            'expiry' => $token->getExpiryDate(OAuthToken::ACCESS_TOKEN),
            'create_date' => date('Y-m-d H:i:s')
        ];

        return $token->generateToken($jwtPayload, OAuthToken::ACCESS_TOKEN);
    }
}
