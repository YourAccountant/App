<?php

namespace Core\Router;

use \Core\Debug\Debug;
use \Core\Support\Arr;

class Request
{
    public $fullUrl;

    public $parsedUrl;

    public $host;

    public $path;

    public $method;

    public $body;

    public $headers;

    public $agent;

    public $requestTime;

    public $queryParameters;

    public $params;

    public $back;

    public function __construct()
    {
        $this->fullUrl = self::getFullUrl();
        $this->parsedUrl = parse_url($this->fullUrl);
        $this->parsedUrl['path'] = preg_replace("/\/\/{2,}/", "", $this->parsedUrl['path']);
        $this->path = $this->parsedUrl['path'] ?? "/";
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = file_get_contents('php://input');
        $this->requestTime = $_SERVER['REQUEST_TIME'];
        $this->host = $this->parsedUrl['host'];
        $this->params = [];
        $this->back = $_SESSION['app']['back'] ?? null;
        $this->headers = Arr::toObject(getallheaders());
        $this->cookies = Arr::toObject($_COOKIE);
        $this->files = Arr::toObject($_FILES ?? []);

        Debug::add('routing', 'request', get_object_vars($this));
    }

    public function set($field, $data)
    {
        if (!isset($this->$field)) {
            $this->$field = $data;
        }
    }

    public function json()
    {
        return json_decode($this->body);
    }

    public static function getFullUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function getHost()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    }

    public static function getIp()
    {
        return isset($_SERVER['HTTP_CLIENT_IP'])
            ? $_SERVER['HTTP_CLIENT_IP']
            : isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                ? $_SERVER['HTTP_X_FORWARDED_FOR']
                : $_SERVER['REMOTE_ADDR'];
    }

    public function pathIncludes($path)
    {
        return strpos($this->path, $path) !== false;
    }
}
