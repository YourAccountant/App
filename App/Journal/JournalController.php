<?php

namespace App\Journal;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;
use \App\Util\Price;
use \Core\Debug\Debug;
use \Core\Support\Obj;

class JournalController extends Controller
{
    public function get(Request $req, Response $res)
    {
        $journal = new Journal();

        $result = $journal->getByAdministration($req->params->administrationId, 'journal');
        $result = Obj::toArray($result);

        return $res->json($result, 200);
    }

    public function getOne(Request $req, Response $res)
    {
        if (!$this->isset($req->params, 'journalId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $journal = new Journal();
        $result = $journal->getOne('journal', $req->params->journalId);
        $result = Obj::toArray($result);

        return $res->json($result, 200);
    }

    public function create(Request $req, Response $res)
    {
        $body = $req->json();

        $err = false;

        if (!$this->isset($body, ['entries', 'desc', 'accountId'])) {
            return $res->json([
                'error' => 'Missing required fields'
            ], 400);
        }

        $data = [
            'accountId' => $body->accountId,
            'administrationId' => $req->params->administrationId,
            'desc' => $body->desc,
            'type' => 'journal'
        ];

        if ($this->isset($body, 'period')) {
            $data['period'] = $body->period;
        }

        if ($this->isset($body, 'openingBalance')) {
            $data['openingBalance'] = $body->openingBalance;
        }

        if ($this->isset($body, 'reference')) {
            $data['reference'] = $body->reference;
        }

        foreach ($body->entries as $entry) {
            if (!$this->isset($entry, ['accountId', 'price', 'desc'])) {
                $err = true;
                break;
            }

            $data['entries'][] = [
                'accountId' => $entry->accountId,
                'price' => $entry->price,
                'desc' => $entry->desc
            ];
        }

        if ($err) {
            return $res->json([
                'error' => 'Missing required fields'
            ], 400);
        }

        $journal = new Journal();
        $journalId = $journal->insertFull($data);

        return $res->json([
            'result' => true
        ], 201);
    }

    public function update(Request $req, Response $res)
    {
        $body = $req->json();

        $data = [];

        if ($this->isset($body, 'entries')) {
            foreach ($body->entries as $entry) {
                if ($this->isset($entry, 'accountId')) {
                    $data['entries'][$entry->id]['accountId'] = $entry->accountId;
                }

                if ($this->isset($entry, 'price')) {
                    $data['entries'][$entry->id]['price'] = $entry->price;
                }

                if ($this->isset($entry, 'desc')) {
                    $data['entries'][$entry->id]['desc'] = $entry->desc;
                }
            }
        }

        if ($this->isset($body, 'desc')) {
            $data['desc'] = $body->desc;
        }

        if ($this->isset($body, 'period')) {
            $data['period'] = $body->period;
        }

        if ($this->isset($body, 'reference')) {
            $data['reference'] = $body->reference;
        }

        if ($this->isset($body, 'openingBalance')) {
            $data['openingBalance'] = $body->openingBalance;
        }

        $journal = new Journal();
        $journal->updateFull($req->params->journalId, $data);

        return $res->json([
            'result' => true
        ], 200);
    }

    public function delete(Request $req, Response $res)
    {
        if (!$this->isset($req->params, 'journalId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $journal = new Journal();
        $journal->deleteFull('journal', $req->params->journalId);

        return $res->json([
            'result' => true
        ], 200);
    }
}
