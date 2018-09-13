<?php

namespace App\Administration;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class AdministrationController extends Controller
{
    public function get(Request $req, Response $res)
    {
        $admin = new Administration();
        $administrations = $admin->getByClient($this->getService('AuthService.getClientId'));
        return $res->json($administrations, 200);
    }

    public function create(Request $req, Response $res)
    {
        $body = $req->json();

        if (!$this->isset($body, 'code')) {
            return $res->json([
                "error" => "missing code"
            ], 400);
        } elseif (!$this->isset($body, 'name')) {
            return $res->json([
                "error" => "missing name"
            ], 400);
        }

        $clientId = $this->getService("AuthService.getClientId");

        $admin = new Administration();

        if ($admin->administrationCodeExists($body->code)) {
            return $res->json([
                "error" => "code exists"
            ], 400);
        }

        $admin->insert([
            "client_id" => $clientId,
            "code" => $body->code,
            "name" => $body->name
        ]);

        return $res->json([
            "result" => true
        ], 201);
    }

    public function update(Request $req, Response $res)
    {
        $body = $req->json();

        $clientId = $this->getService("AuthService.getClientId");
        $admin = new Administration();

        if (!$this->isset($req->params, 'administrationId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $id = $req->params->administrationId;

        if (!$admin->isOwned($id)) {
            return $res->json([
                "error" => "forbidden"
            ], 401);
        }

        $update = [];

        if ($this->isset($body, 'code')) {
            $update['code'] = $body->code;
        }

        if ($this->isset($body, 'name')) {
            $update['name'] = $body->name;
        }

        $admin->update($id, $update);

        return $res->json([
            'result' => true
        ], 200);
    }

    public function delete(Request $req, Response $res)
    {
        $clientId = $this->getService("AuthService.getClientId");
        $admin = new Administration();

        if (!$this->isset($req->params, 'administrationId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $id = $req->params->administrationId;

        if (!$admin->isOwned($id)) {
            return $res->json([
                "error" => "forbidden"
            ], 401);
        }

        $admin->delete($id);

        return $res->json([
            'result' => true
        ], 200);
    }
}
