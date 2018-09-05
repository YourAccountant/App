<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Service;
use \App\Client\Client;

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

    public function setAuthModel($id)
    {
        $client = new Client();
        $client->getBy('id', '=', $id);

        if ($client->poolIsEmpty()) {
            return false;
        }

        $this->getApp()->addModel("AuthClient", $client);
        return true;
    }
}
