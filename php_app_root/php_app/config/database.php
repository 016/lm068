<?php

return [
    'host' => 'ee-mysql-5.7.40',
    'database' => 'lm068',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8mb4',
    'port' => 3306,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];