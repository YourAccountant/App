<?php

namespace App\Journal;

use \Core\Foundation\Model;
use \App\Account\Account;

class JournalEntry extends Model
{
    protected $table = "lines";

    public function getLinesByParent($id)
    {
        return $this->getBuilder()
            ->where('parent_type', '=', 'journals')
            ->and('parent_id', '=', $id)
            ->exec()
            ->fetchAll();
    }

    public function insert($data)
    {
        $account = new Account();

        $vat = $account->getBuilder()
            ->columns('`vat`')
            ->where('id', '=', $data['account_id'])
            ->exec()
            ->fetch()->vat;


        return $this->getBuilder()
            ->insert([
                'account_id' => $data['account_id'],
                'price' => $data['price'],
                'vat' => $vat,
                'desc' => $data['desc'],
                'parent_type' => 'journals',
                'parent_id' => $data['parent_id']
            ])
            ->exec();
    }
}
