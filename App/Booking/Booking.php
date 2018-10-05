<?php

namespace App\Booking;

use \Core\Foundation\Model;
use \App\Util\Price;

class Booking extends Model
{
    protected $table = "bookings";

    public function getOne($type, $id)
    {
        $booking = $this->getBuilder()
            ->where('id', '=', $id)
            ->exec()
            ->fetch();

        $entry = new BookingEntry();

        $booking->entries = $entry->getByParent($id);

        return $booking;
    }

    public function getByAdministration($adminId, $type)
    {
        $bookings = $this->getBuilder()
            ->where('administrationId', '=', $adminId)
            ->orderBy('`createdAt` DESC')
            ->limit(999)
            ->exec()
            ->fetchAll();

        $result = [];

        foreach ($bookings as $booking) {
            $entry = new BookingEntry();
            $booking->entries = $entry->getByParent($booking->id);
            $result[] = $booking;
        }

        return $result;
    }

    public function insertFull($data)
    {
        $bookingEntry = new BookingEntry();

        $bookingId = $this->getBuilder()
            ->insert([
            'administrationId' => $data['administrationId'],
            'accountId' => $data['accountId'],
            'type' => $data['type'],
            'desc' => $data['desc'],
            'period' => ($data['period'] ?? date('Y-m')) . '-01',
            'openingBalance' => $data['openingBalance'] ?? 0,
            'reference' => $data['reference'] ?? null
        ])->exec();

        foreach ($data['entries'] as $entry) {
            $bookingEntry->insert([
                'accountId' => $entry['accountId'],
                'price' => $entry['price'],
                'desc' => $entry['desc'],
                'bookingId' => $bookingId
            ]);
        }
    }

    public function updateFull($id, $data)
    {
        $update = [];

        $entries = $data['entries'] ?? [];
        unset($data['entries']);
        $this->update($id, $data);

        $bookingEntry = new BookingEntry();
        foreach ($entries as $entryId => $entry) {
            $bookingEntry->update($entryId, $entry);
        }

        return true;
    }

    public function deleteFull($type, $id)
    {
        $entry = new BookingEntry();
        $entry->getBuilder()
            ->where('bookingId', '=', $id)
            ->delete()
            ->exec();

        $this->delete($id);
    }
}
