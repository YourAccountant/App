<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Model;
use \Core\Support\Str;
use \Core\Router\Request;

class OAuthToken extends Model
{
    protected $table = 'oauth_tokens';

    protected $daysAfterExpiry = 1;

    public function generateToken()
    {
        $token = bin2hex(random_bytes(64));

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

        return $this;
    }

    public function checkExpiry($date = null)
    {
        $date = $date ?? $this->get('expiry');
        return $date > date('Y-m-d H:i:s');
    }

    public function getExpiryDate()
    {
        return date('Y-m-d H:i:s', strtotime("+{$this->daysAfterExpiry} days"));
    }

    public function getByRelation($type, $clientId, $partnerId)
    {
        return $this->getDependencies('Connection')
                    ->builder($this->table)
                    ->where('oauth_partner_id', '=', $partnerId)
                    ->and('client_id', '=', $clientId)
                    ->and('token_type', '=', $type)
                    ->limit(1)
                    ->exec()
                    ->fetch();
    }

    public function create($type, $clientId, $partnerId = null)
    {
        if ($type == 'refresh_token' && $partnerId != null) {
            $token = $this->getByRelation('refresh_token', $clientId, $partnerId);

            if (!empty($token)) {
                $this->update($token->id, [
                    'token' => $this->generateToken(),
                    'expiry' => $this->getExpiryDate()
                ]);

                return $token->id;
            }
        }

        $data = [
            'client_id' => $clientId,
            'token_type' => $type,
            'token' => $this->generateToken(),
            'expiry' => $this->getExpiryDate()
        ];

        if ($partnerId != null) {
            $data['oauth_partner_id'] = $partnerId;
        }

        if (Request::isAjax()) {
            $data['is_app'] = 1;
        }

        return $this->insert($data);
    }

    public function refresh($id)
    {
        $token = $this->generateToken();
        $expiry = $this->getExpiryDate();

        $this->update($id, [
            'token' => $token,
            'expiry' => $expiry
        ]);

        if (!$this->poolIsEmpty()) {
            $this->pool->token = $token;
            $this->pool->expiry = $expiry;
        }

        return $this;
    }
}
