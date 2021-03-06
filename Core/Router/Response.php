<?php

namespace Core\Router;

use \Core\Template\View;

class Response
{
    public $code = 200;

    public $headers = [];

    public $cookies = [];

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

    public function send($content = null, $code = 200)
    {
        if (is_array($content)) {
            $this->addHeader('Content-type', 'application/json');
        }

        $this->code = $code;
        $this->hasResponse = true;
        $this->content = is_array($content) ? json_encode($content) : $content;

        return $this;
    }

    public function json($content, $code = 200)
    {
        $this->addHeader('Content-type', 'application/json');
        return $this->send($content, $code);
    }

    public function xml($content, $code = 200)
    {
        $this->addHeader('Content-type', 'text/xml');
        return $this->send($content, $code);
    }

    public function run()
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        http_response_code($this->code ?? 200);

        foreach ($this->cookies as $name => $value) {
            list($val, $expiry) = $value;
            setcookie($name, $val, $expiry, "/");
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

    public function addCookie($name, $value, $expiry = null)
    {
        $expiry = $expiry ?? date('Y-m-d H:i:s', strtotime("+1 hour"));

        if ($expiry > -1) {
            $expiry = strtotime($expiry);
        }

        $this->cookies[$name] = [$value, $expiry];
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

    public static function setBack()
    {
        if (!empty($_SESSION['app']['history'])) {
            if ($_SESSION['app']['back'] == $_SESSION['app']['history'][count($_SESSION['app']['history']) - 1]) {
                return;
            }

            $_SESSION['app']['back'] = $_SESSION['app']['history'][count($_SESSION['app']['history']) - 1];
        }
    }

    public static function addHistory()
    {
        $url = Request::getFullUrl();

        if (!empty($_SESSION['app']['history'])) {
            if ($_SESSION['app']['history'][count($_SESSION['app']['history']) -1] == $url) {
                return;
            }
        }

        $_SESSION['app']['history'][] = $url;
    }
}
