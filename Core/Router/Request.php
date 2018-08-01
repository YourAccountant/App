<?php

namespace Core\Router;

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

    public $anchor;

    public function __construct()
    {
        $this->fullUrl = self::getFullUrl();
        $this->parsedUrl = parse_url($this->fullUrl);
        $this->path = $this->parsedUrl['path'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = file_get_contents('php://input');
        $this->headers = \getallheaders();
        $this->requestTime = $_SERVER['REQUEST_TIME'];
        $this->queryParameters = $this->parsedUrl['query'] ?? [];
        $this->anchor = $this->parsedUrl['anchor'] ?? '';
        $this->host = $this->parsedUrl['host'];
    }

    public static function getFullUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}
