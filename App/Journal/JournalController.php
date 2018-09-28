<?php

namespace App\Journal;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class JournalController extends Controller
{
    public function get(Request $req, Response $res)
    {
        $journal = new Journal();
        $journals = $journal->getJournalsFromAdministration($req->params->administrationId);
        $result = [];

        foreach ($journals as $key => $journal) {
            $journalEntry = new JournalEntry();
            $journal->entries = $journalEntry->getLinesByParent($journal->id);

            $result[] = $journal;
        }

        return $res->json($result, 200);
    }

    public function getOne(Request $req, Response $res)
    {
        if ($req->params->journalId == null) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $journal = new Journal();
        $journal->getBy('id', '=', $req->params->journalId);

        $journalEntry = new JournalEntry();
        $journal->set('entries', $journalEntry->getLinesByParent($journal->get('id')));

        return $res->json($journal, 200);
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

        foreach ($body->entries as $entry) {
            if (!$this->isset($entry, ['accountId', 'price', 'desc'])) {
                $err = true;
                break;
            }
        }

        if ($err) {
            return $res->json([
                'error' => 'Missing required fields'
            ], 400);
        }

        $data = [
            'account_id' => $body->accountId,
            'administration_id' => $req->params->administrationId,
            'desc' => $body->desc
        ];

        $journal = new Journal();
        $journalId = $journal->insert($data);

        foreach ($body->entries as $entry) {
            $journalEntry = new JournalEntry();

            $data = [
                'account_id' => $entry->accountId,
                'price' => $entry->price,
                'desc' => $entry->desc,
                'parent_id' => $journalId
            ];

            $journalEntry->insert($data);
        }

        return $res->json([
            'result' => true
        ], 201);
    }

    public function update(Request $req, Response $res)
    {
    }

    public function delete(Request $req, Response $res)
    {
    }
}
