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
        $this->code = $code;
        $this->hasResponse = true;
        $this->content = is_array($content) ? json_encode($content) : $content;

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
