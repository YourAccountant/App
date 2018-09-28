<?php

namespace App\Journal;

use \Core\Foundation\Model;
use \App\Util\Price;
use \App\Account\Account;

class Journal extends Model
{
    protected $table = "journals";

    public function getJournalsFromAdministration($id)
    {
        return $this->getBuilder()
            ->where('administration_id', '=', $id)
            ->orderBy('`created_at` DESC')
            ->exec()
            ->fetchAll();
    }
}
