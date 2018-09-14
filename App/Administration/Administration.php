<?php

namespace App\Administration;

use \Core\Foundation\Model;

class Administration extends Model
{
    protected $table = 'administrations';

    public function getByClient($clientId, $orderBy = ' created_at DESC ')
    {
        $result = $this->getBuilder()
            ->where('client_id', '=', $clientId)
            ->orderBy($orderBy)
            ->exec()
            ->fetchAll();

        $this->setPool($result);
        return $this;
    }

    public function administrationCodeExists($code)
    {
        return $this->getBuilder()
            ->columns('COUNT(id) as total')
            ->where('code', '=', $code)
            ->and('client_id', '=', $this->getService('AuthService.getClientId'))
            ->limit(1)
            ->exec()
            ->fetch()
            ->total > 0;
    }

    public function isOwned($id)
    {
        return $this->getBuilder()
            ->columns('COUNT(id) as total')
            ->where('id', '=', $id)
            ->and('client_id', '=', $this->getService('AuthService.getClientId'))
            ->limit(1)
            ->exec()
            ->fetch()
            ->total > 0;
    }
}
