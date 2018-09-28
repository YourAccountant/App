<?php

namespace App\Util;

class Price
{
    public $price;
    public $percentage;
    public $cents;
    public $result;

    public function __construct($price, $percentage = null)
    {
        $this->price = $price;
        $this->cents = $price * 100;
        $this->percentage = $percentage;
    }

    public function getVatTotal()
    {
        return ((($this->cents / floatval("1.$this->percentage")) - $this->cents) * -1);
    }

    public function getNetTotal()
    {
        return $this->cents - $this->getVatTotal();
    }

    public function getGrossTotal()
    {
        return $this->cents;
    }

    public function toFloat()
    {
        return floatval($this->cents / 100);
    }
}
