<?php

namespace App\Account;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;
use \App\Administration\Administration;
use \Core\Support\Arr;

class AccountController extends Controller
{
    public function get(Request $req, Response $res)
    {
        $account = new Account();
        $accounts = $account->getByAdministration($req->params->administrationId);

        return $res->json($accounts, 200);
    }

    public function getOne(Request $req, Response $res)
    {
        if (!$this->isset($req->params, 'accountId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $adminId = $req->params->administrationId;
        $accountId = $req->params->accountId;

        $account = new Account();
        $account->getBy('id', '=', $accountId);

        return $res->json($account, 200);
    }

    public function create(Request $req, Response $res)
    {
        $body = $req->json();

        if (!$this->isset($body, ['code', 'desc', 'type', 'vat'])) {
            return $res->json([
                'error' => 'Missing required fields'
            ], 400);
        }

        $adminId = $req->params->administrationId;
        $account = new Account();

        if ($account->codeExists($adminId, $body->code)) {
            return $res->json([
                'error' => 'code exists'
            ], 400);
        }

        $data = [
            'administration_id' => $adminId,
            'code' => $body->code,
            'desc' => $body->desc,
            'type' => $body->type,
            'vat' => $body->vat
        ];

        if (isset($body->isBalance)) {
            $data['is_balance'] = $body->isBalance ? "1" : "0";
        }

        if (isset($body->isCredit)) {
            $data['is_credit'] = $body->isCredit ? "1" : "0";
        }

        $account->insert($data);

        return $res->json([
            'result' => true
        ], 201);
    }

    public function update(Request $req, Response $res)
    {
        $body = $req->json();

        if (!$this->isset($req->params, 'accountId')) {
            return $res->json([
                'error' => 'missing accountId'
            ], 400);
        }

        $accountId = $req->params->accountId;

        $data = Arr::addIfSet([], $body, ['code', 'desc', 'type', 'vat']);

        if (isset($body->isBalance)) {
            $data['is_balance'] = $body->isBalance ? "1" : "0";
        }

        if (isset($body->isCredit)) {
            $data['is_credit'] = $body->isCredit ? "1" : "0";
        }

        if (isset($body->isActive)) {
            $data['is_active'] = $body->isActive ? "1" : "0";
        }

        $account = new Account();
        $account->update($accountId, $data);

        return $res->json([
            'result' => true
        ], 200);
    }

    public function delete(Request $req, Response $res)
    {
        (new Account())->delete($req->params->accountId);

        return $res->json([
            'result' => true
        ],200);
    }
}
