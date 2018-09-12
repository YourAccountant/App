<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Model;

class OAuthPartner extends Model
{
    protected $table = 'oauth_partners';

    public function generateSlug($name)
    {
        $name = trim($name);
        $name = preg_replace("/\s{1,}|\r\n|\n|\t/", "-", $name);
        $name = preg_replace("/[^a-zA-Z0-9\-]/", "", $name);
        return $name;
    }

    public function generateSecret()
    {
        $secret = substr(bin2hex(random_bytes(16)), 0, 25);
        if ($this->exists('secret', '=', $secret)) {
            return $this->generateSecret();
        }

        return $secret;
    }

    public function create($clientId, $data)
    {
        $data['slug'] = $this->generateSlug($data['name']);
        $data['secret'] = $this->generateSecret();
        return $this->insert([
            'client_id' => $clientId,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'desc' => $data['desc'] ?? '',
            'secret' => $data['secret']
        ]);
    }
}
