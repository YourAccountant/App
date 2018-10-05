<?php

namespace App\Booking;

use \Core\Foundation\Model;
use \App\Util\Price;

class BookingEntry extends Model
{
    protected $table = "lines";

    public function getByParent($id)
    {
        return $this->getBuilder()
            ->where('bookingId', '=', $id)
            ->exec()
            ->fetchAll();
    }
}
