<?php

namespace App\Journal;

use \App\Util\Price;
use \App\Account\Account;

class Journal extends \App\Booking\Booking
{
    protected $table = "bookings";

    public function getJournalsFromAdministration($id)
    {
        return $this->getBuilder()
            ->where('administrationId', '=', $id)
            ->and('type', '=', 'journal')
            ->orderBy('`createdAt` DESC')
            ->exec()
            ->fetchAll();
    }
}
