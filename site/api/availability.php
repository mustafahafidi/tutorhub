<?php

require(__DIR__. '/common.php');

use TutorHub\Booking;
use TutorHub\Session;
use TutorHub\Slot;
use TutorHub\Subject;
use TutorHub\Teaching;
use TutorHub\User;
use Utils\Arrays;

Session::start();
if (!Session::isLogged())
    die('Not logged in.');

$u = Session::getUser();
if ($u['type'] != User::TYPE_TUTOR)
    die('Must be a tutor.');



if(isset($_POST["save"])) {
    /*da salvare i dati arrivati */
    echo "Dati salvati";
    die();
}


if (!isset($_GET['start']) || !isset($_GET['end']))
    die('Invalid parameters.');
$start = new DateTime($_GET['start']);
$end = new DateTime($_GET['end']);

$db = \TutorHub\getDb();

$slots = Slot::where($db, [['tutor' => $u->getKey()]]);
$slots = array_filter($slots, function ($slot) use (&$start, &$end) {
    $slotStart = new DateTime($slot['start']);
    $slotEnd = new DateTime($slot['end']);
    return !($slotStart > $end || $slotEnd < $start);
});

$excludeBookings = Booking::where($db, Arrays::map(function ($slot) { return [
    'slot' => $slot->getKey(),
    'status' => Booking::STATUS_ACCEPTED
]; }, $slots));

$excludeSlots = Arrays::column($excludeBookings, 'slot');
$slots = array_filter($slots, function ($slot) use (&$excludeSlots) {
    return !(in_array($slot->getKey(), $excludeSlots));
});

$events = [];
foreach ($slots as $slot) {
    $events[] = [
        'start' => $slot['start'],
        'end' => $slot['end']
    ];
}

echo json_encode($events);
