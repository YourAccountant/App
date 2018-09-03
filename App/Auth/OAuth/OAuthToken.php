<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Model;
use \Core\Support\Str;

class OAuthToken extends Model
{
    protected $table = 'oauth_tokens';

    protected $tokenLength = 32;

    protected $daysAfterExpiry = 1;

    public function generateToken()
    {
        $token = Str::generateRandomString($this->tokenLength);

        if ($this->exists('token', '=', $token)) {
            return $this->generateToken();
        }

        return $token;
    }

    public function getByBearer($token)
    {
        $this->pool = $this->getDependencies('Connection')
                ->builder($this->table)
                ->where('token_type', '=', 'bearer')
                ->and('token', '=', $token)
                ->exec()
                ->fetch();

        $this->setModelByPool();
        return $this;
    }

    public function getByRefreshToken($token)
    {
        $this->pool = $this->getDependencies('Connection')
                ->builder($this->table)
                ->where('token_type', '=', 'refresh_token')
                ->and('token', '=', $token)
                ->exec()
                ->fetch();

        $this->setModelByPool();
        return $this;
    }

    public function getExpiryDate()
    {
        return date('Y-m-d H:i:s', strtotime("+{$this->daysAfterExpiry} days"));
    }

    public function create($type, $partnerId, $clientId)
    {
        return $this->insert([
            'oauth_partner_id' => $partnerId,
            'client_id' => $clientId,
            'token_type' => $type,
            'token' => $this->generateToken(),
            'date_expiration' => $this->getExpiryDate()
        ]);
    }

    public function refresh($id)
    {
        $token = $this->generateToken();
        $expiry = $this->getExpiryDate();

        $this->update($id, [
            'token' => $token,
            'date_expiration' => $expiry
        ]);

        if (!$this->poolIsEmpty()) {
            $this->pool->token = $token;
            $this->pool->date_expiration = $expiry;
        }

        return $this;
    }
}
