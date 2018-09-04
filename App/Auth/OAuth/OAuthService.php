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
                    'type' => 'refresh_token',
                    'refresh_token' => $token->get('token'),
                    'expiry_date' => $token->get('date_expiration'),
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
