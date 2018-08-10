<?php

namespace Core\Router;

use \Core\Template\View;

class Response
{
    public $code = 200;

    public $headers = [];

    public $cookies = [];

    public $body = '';

    public $params = [];

    public $hasResponse = false;

    public function view($view, $layout = null, $data = [])
    {
        $this->code = 200;
        $this->addHeader('Content-type', 'text/html');
        View::setData($data);
        $layout = $layout ?? $this->layout ?? null;
        $this->send(View::serve($view, $layout, true));
    }

    public function send($content = null)
    {
        $this->hasResponse = true;
        if (is_array($content)) {
            $this->content = json_encode($content);
        } else {
            $this->content = $content;
        }

        return $this;
    }

    public function run()
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        http_response_code($this->code ?? 200);

        foreach ($this->cookies as $name => $value) {
            setcookie($name, $value);
        }

        echo $this->content ?? "";
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function addCookie($name, $value = "")
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    public function hasResponse()
    {
        return $this->hasResponse;
    }

    public static function redirect($url)
    {
        header("Location: $url");
        die;
    }

    public static function back()
    {
        if (!isset($_SESSION['app']['back'])) {
            return false;
        }

        self::redirect($_SESSION['app']['back']);
    }

    public static function setBack($url)
    {
        $_SESSION['app']['back'] = $url;
        return $url;
    }

}
