<?php

namespace TutorHub;

use Database\Entity;

class Subject extends Entity
{
    protected static $dbTable = 'subjects';
    protected static $dbKey = 'idSubject';
    protected static $arrayMap = [
        'name' => 'name'
    ];
}
