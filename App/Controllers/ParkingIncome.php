<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\ParkingTicket;

/**
 * ParkingIncome controller
 *
 * PHP version 7.3.0
 */
class ParkingIncome extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $from = '';
        $to = '';
        $total_income = 0;

        if (isset($_GET['from']) || isset($_GET['to'])) {
            $fromtime = strtotime($_GET['from']);
            $totime = strtotime($_GET['to']);

            $from = date('Y-m-d H:m:s', $fromtime);
            $to = date('Y-m-d H:m:s', $totime);

            $parking_tickets = ParkingTicket::getParkingTickets($from, $to);
            $total_income = $this->calculateTotalIncome($fromtime, $totime, $parking_tickets);
        } else {
            $parking_tickets = ParkingTicket::getAllParkingTickets();
        }

        View::renderTemplate('ParkingIncome/index.html', [
            'parking_tickets' => $parking_tickets,
            'from' => $from,
            'to' => $to,
            'total_income' => $total_income
        ]);
    }

    /**
     * Calculate Total Income For Given Time Period
     *
     * @return float
     */
    public function calculateTotalIncome($fromtime, $totime, $parking_tickets)
    {
        $total_income = 0;

        foreach ($parking_tickets as $parking_ticket) {
            $vehicle_income = $this->calculateIncomePerVehicle($fromtime, $totime, $parking_ticket);
            $total_income = $total_income + $vehicle_income;
        }

        return $total_income;
    }

    /**
     * Calculate Income Per Vehicle For Given Time Period
     *
     * @return float
     */
    public function calculateIncomePerVehicle($fromtime, $totime, $parking_ticket)
    {
        $vehicle_income = 0;

        $parking_started = strtotime($parking_ticket->created_at);
        // if parking started before the time period
        if ($parking_started < $fromtime) {
            $parking_started = $fromtime;
        }

        $parking_ended = strtotime($parking_ticket->paid_at);
        // if parking is longer than the time period
        if ($parking_ended > $totime) {
            $parking_ended = $totime;
        }

        // calculate the hours charged for the vehicle in the time period
        $timediff = $parking_ended - $parking_started;
        $hours_charged = $timediff / 3600; // convert timediff from seconds to hours
        $hours_charged = ceil($hours_charged); // round hours charged up as time charged is at the start of the hour

        $vehicle_income = $hours_charged * $parking_ticket->price;

        return $vehicle_income;
    }
}
