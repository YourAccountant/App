<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Service;

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
}
