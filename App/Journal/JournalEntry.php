<?php

namespace App\Journal;

use \Core\Foundation\Model;
use \App\Account\Account;

class JournalEntry extends \App\Booking\BookingEntry
{
    protected $table = "lines";
}
