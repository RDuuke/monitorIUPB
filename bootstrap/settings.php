<?php
return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => getenv("DB_HOST"),
            'database' => getenv("DB_NAME"),
            'username' => getenv("DB_USER"),
            'password' => getenv("DB_PASSWORD"),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options' => [\PDO::ATTR_EMULATE_PREPARES => true],
        ],
        'db_moodle' => [
            'driver' => 'mysql',
            'host' => getenv("DB_HOST"),
            'database' => getenv("DB_NAME_MOODLE"),
            'username' => getenv("DB_USER"),
            'password' => getenv("DB_PASSWORD"),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options' => [\PDO::ATTR_EMULATE_PREPARES => true],
        ],
    ],
];