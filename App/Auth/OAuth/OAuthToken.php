<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Model;
use \Core\Support\Str;

class OAuthToken extends Model
{
    protected $table = 'oauth_tokens';

    protected $tokenLength = 32;

    public function generateToken()
    {
        $token = Str::generateRandomString($this->tokenLength);

        if ($this->exists('token', '=', $token)) {
            return $this->generateToken();
        }

        return $token;
    }
}
