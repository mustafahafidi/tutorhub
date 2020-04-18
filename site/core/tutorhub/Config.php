<?php

namespace TutorHub;

class Config
{
    /**
     * Gets the global configuration.
     *
     * @return array Configuration.
     */
    public static function get()
    {
        return [
            'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
            'db_port' => $_ENV['DB_PORT'] ?? 3306,
            'db_name' => $_ENV['DB_NAME'] ?? '',
            'db_user' => $_ENV['DB_USER'] ?? '',
            'db_pass' => $_ENV['DB_PASS'] ?? '',
            'upload_webroot' => 'uploads',
            'upload_root' => __DIR__ . '/../../uploads',
            'upload_maxsize' => 128*1024,
            'upload_maxwidth' => 600,
            'upload_maxheight' => 600,
            'upload_exts' => ['gif', 'jpg', 'jpeg', 'png']
        ];
    }
}

