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
                    'tokenType' => OAuthToken::REFRESH_TOKEN,
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
            'tokenType' => $type,
            'clientId' => $clientId,
            'expiry' => $expiry,
            'createDate' => date('Y-m-d H:i:s'),
            'partnerId' => $partnerId
        ];

        $token = new OAuthToken();

        $jwt = $token->generateToken($payload, $type);

        $token->insert([
            'clientId' => $clientId,
            'oauthPartnerId' => $partnerId,
            'tokenType' => $type,
            'token' => $jwt
        ]);

        return $jwt;
    }

    public function createSessionToken($clientId, $remindMe = false)
    {
        $type = OAuthToken::SESSION_TOKEN;


        $expiry = $remindMe ? date('Y-m-d H:i:s', strtotime("+1 years")) : OAuthToken::getExpiryDate($type);

        $payload = [
            'tokenType' => $type,
            'clientId' => $clientId,
            'expiry' => $expiry,
            'ip' => Request::getIp(),
            'createDate' => date('Y-m-d H:i:s')
        ];

        $token = new OAuthToken();
        $jwt = $token->generateToken($payload, $type);

        $session = new Session();

        $session->insert([
            'clientId' => $clientId,
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
            'tokenType' => OAuthToken::ACCESS_TOKEN,
            'token' => $refreshToken,
            'clientId' => $payload->clientId,
            'expiry' => $token->getExpiryDate(OAuthToken::ACCESS_TOKEN),
            'createDate' => date('Y-m-d H:i:s')
        ];

        return $token->generateToken($jwtPayload, OAuthToken::ACCESS_TOKEN);
    }
}
