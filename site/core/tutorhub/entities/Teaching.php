<?php

namespace TutorHub;

use Database\Entity;

class Teaching extends Entity
{
    protected static $dbTable = 'teachings';
    protected static $dbKey = 'idTeaching';
    protected static $arrayMap = [
        'subject' => 'idSubject',
        'tutor' => 'idTutor',
        'price' => 'price'
    ];
}
