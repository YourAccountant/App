<?php

namespace Core\Config;

class Config
{

    private $path;

    private $raw;

    public function __construct($path)
    {
        $this->path = $path;
        $this->get();
        $this->serialize();
    }

    public function get()
    {
        if (file_exists($this->path)) {
            $this->raw = \file_get_contents($this->path);
            return true;
        }

        return false;
    }

    public function serialize()
    {
        $items = preg_split("/\r\n|\n/", $this->raw);
        foreach ($items as $item) {
            if (trim($item) == null || strpos($item, "=") === false) {
                continue;
            }

            list($key, $value) = explode("=", $item);

            $key = trim($key);
            $value = trim($value);

            $this->{$key} = $value;
        }
    }
}
