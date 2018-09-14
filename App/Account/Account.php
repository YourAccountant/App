<?php

namespace App\Account;

use \Core\Foundation\Model;

class Account extends Model
{
    protected $table = 'accounts';

    public function getByAdministration($adminId)
    {
        return $this->getBuilder()
            ->where('administration_id', '=', $adminId)
            ->limit(1000)
            ->exec()
            ->fetchAll();
    }

    public function codeExists($adminId, $code)
    {
        return $this->getBuilder()
            ->columns('COUNT(id) AS total')
            ->where('administration_id', '=', $adminId)
            ->and('code', '=', $code)
            ->limit(1)
            ->exec()
            ->fetch()
            ->total > 0;
    }
}
