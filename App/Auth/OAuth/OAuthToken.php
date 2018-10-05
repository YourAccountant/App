<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Model;
use \Core\Support\Str;
use \Core\Support\Arr;
use \Core\Router\Request;
use \Firebase\JWT\JWT;

class OAuthToken extends Model
{
    // OAuth token for partners to refresh access token
    const REFRESH_TOKEN = 'refresh_token';

    // OAuth token for partner
    const ACCESS_TOKEN = 'access_token';

    // Session token for client related to phpsessid
    const SESSION_TOKEN = 'session_token';

    protected $table = 'oauthTokens';

    protected $daysAfterExpiry = 1;

    private static $secret = "jwt_secret"; // this is temporary

    public function __construct()
    {
        if (self::$secret == null) {
            self::$secret = $this->getConfig('jwtSecret');
        }
    }

    public static function getSecret($type)
    {
        return self::$secret;
    }

    public function generateToken($payload, $type)
    {
        $token = JWT::encode($payload, self::getSecret($type));

        if ($this->exists('token', '=', $token)) {
            return $this->generateToken($payload, $type);
        }

        return $token;
    }

    public static function decodeToken($token, $type)
    {
        $token = str_replace("Bearer ", "", $token);

        try {
            $payload = JWT::decode($token, self::getSecret($type), ['HS256']);
        } catch (\Exception $e) {
            $payload = null;
        }

        return Arr::toObject($payload);
    }

    public static function checkExpiry($date = null)
    {
        return $date > date('Y-m-d H:i:s');
    }

    public function getExpiryDate($type = self::REFRESH_TOKEN)
    {
        if ($type == self::REFRESH_TOKEN) {
            return date('Y-m-d H:i:s', strtotime("+1 years"));
        } elseif ($type == self::SESSION_TOKEN) {
            return date('Y-m-d H:i:s', strtotime("+1 hours"));
        } elseif ($type == self::ACCESS_TOKEN) {
            return date('Y-m-d H:i:s', strtotime("+1 hours"));
        }
    }

    public function getByRelation($type, $clientId, $partnerId = null)
    {
        return $this->getDependencies('Connection')
            ->builder($this->table)
            ->where('tokenType', '=', $type)
            ->and('clientId', '=', $clientId)
            ->and('oauthPartnerId', '=', $partnerId)
            ->limit(1)
            ->exec()
            ->fetch();
    }
}
