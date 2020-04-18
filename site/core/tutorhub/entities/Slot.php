<?php

namespace TutorHub;

use Database\Entity;

class Slot extends Entity
{
    protected static $dbTable = 'slots';
    protected static $dbKey = 'idSlot';
    protected static $arrayMap = [
        'start' => 'initialDate',
        'end' => 'endDate',
        'tutor' => 'idTutor'
    ];
}
