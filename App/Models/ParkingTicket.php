<?php

namespace App\Models;

use PDO;

/**
 * ParkingTicket model
 *
 * PHP version 7.3.0
 */
class ParkingTicket extends \Core\Model
{
    /**
     * Get ALL Parking Tickets
     *
     * @return array
     */
    public static function getAllParkingTickets()
    {
        $sql = 'SELECT parking_ticket.created_at, parking_ticket.paid_at, vehicle.type as vehicle_type, vehicle.registration_number, parking_spot.number, parking_spot.price
                FROM parking_ticket, vehicle, parking_spot
                WHERE parking_ticket.vehicle_id = vehicle.id AND vehicle.parking_spot_number = parking_spot.number
                ORDER BY created_at ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get Parking Tickets From To
     *
     * @return array
     */
    public static function getParkingTickets($from, $to)
    {
        $sql = 'SELECT parking_ticket.created_at, parking_ticket.paid_at, vehicle.type as vehicle_type, vehicle.registration_number, parking_spot.number, parking_spot.price
                FROM parking_ticket, vehicle, parking_spot
                WHERE parking_ticket.vehicle_id = vehicle.id AND vehicle.parking_spot_number = parking_spot.number AND created_at < :to AND paid_at > :from AND is_paid = 1
                ORDER BY created_at ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':from', $from, PDO::PARAM_STR);
        $stmt->bindValue(':to', $to, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
