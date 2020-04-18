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
if (!isset($_GET['start']) || !isset($_GET['end']))
    die('Invalid parameters.');
$start = new DateTime($_GET['start']);
$end = new DateTime($_GET['end']);

$u = Session::getUser();
$t = $u['type'] == User::TYPE_TUTOR;

$db = \TutorHub\getDb();

if ($t) {
    $teachings = Teaching::where($db, [['tutor' => $u->getKey()]]);
    $bookings = Booking::where($db, Arrays::map(function ($teaching) { return [
        'teaching' => $teaching->getKey(),
        'status' => Booking::STATUS_ACCEPTED
    ]; }, $teachings));
    $users = User::fromKeys($db, Arrays::column($bookings, 'student'));
} else {
    $bookings = Booking::where($db, [[
        'student' => $u->getKey(),
        'status' => Booking::STATUS_ACCEPTED
    ]]);
    $teachings = Teaching::fromKeys($db, Arrays::column($bookings, 'teaching'));
    $users = User::fromKeys($db, Arrays::column($teachings, 'tutor'));
}

$subjects = Subject::fromKeys($db, Arrays::column($teachings, 'subject'));
$slots = Slot::fromKeys($db, Arrays::column($bookings, 'slot'));

$bookings = array_filter($bookings, function ($booking) use (&$start, &$end, &$slots) {
    $slot = $slots[$booking['slot']];
    $slotStart = new DateTime($slot['start']);
    $slotEnd = new DateTime($slot['end']);
    return !($slotStart > $end || $slotEnd < $start);
});

$events = [];
foreach ($bookings as $booking) {
    $slot = $slots[$booking['slot']];
    $teaching = $teachings[$booking['teaching']];
    $user = $users[$t ? $booking['student'] : $teaching['tutor']];
    $subject = $subjects[$teaching['subject']];
    $title = $subject['name']
        . ' ('
        . $user['name'] . ' ' . $user['surname']
        . (empty($user['city']) ? '' : ' - ' . $user['city'])
        . ')';
    $events[] = [
        'title' => $title,
        'start' => $slot['start'],
        'end' => $slot['end']
    ];
}

echo json_encode($events);
