<?php

namespace TutorHub;

use Database\Entity;

class Booking extends Entity
{
    /**
     * The booking is pending approval from the tutor.
     */
    const STATUS_PENDING = 0;
    /**
     * The booking has been approved by the tutor.
     */
    const STATUS_ACCEPTED = 1;
    /**
     * The booking has been declined by the tutor.
     */
    const STATUS_DECLINED = 2;

    protected static $dbTable = 'bookings';
    protected static $dbKey = 'idBooking';
    protected static $arrayMap = [
        'student' => 'idStudent',
        'slot' => 'idSlot',
        'teaching' => 'idTeaching',
        'status' => 'status',
        'comment' => 'comment'
    ];
}
