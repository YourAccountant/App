<?php

namespace Core\Router;

use \Core\Debug\Debug;

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
        $this->headers = getallheaders();
        $this->requestTime = $_SERVER['REQUEST_TIME'];
        $this->queryParameters = $_GET;
        $this->postParameters = $_POST;
        $this->host = $this->parsedUrl['host'];
        $this->params = [];
        $this->back = $_SESSION['app']['back'] ?? null;

        Debug::add('routing', 'request', get_object_vars($this));
    }

    public static function getFullUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function getHost()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }
}
