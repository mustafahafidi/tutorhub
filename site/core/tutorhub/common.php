<?php

namespace {
    require_once(__DIR__ . '/../database/common.php');
    require_once(__DIR__ . '/entities/common.php');
    require_once(__DIR__ . '/Config.php');
    require_once(__DIR__ . '/ImageUpload.php');
    require_once(__DIR__ . '/Session.php');
}

namespace TutorHub {
    use Database\Database;

    /**
     * Gets the database connection.
     */
    function getDb() : Database
    {
        static $db = null;
        if ($db === null)
            $db = new Database(Config::get());
        return $db;
    }
}
