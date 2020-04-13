<?php
// configuration file
$config = [
    'db' => [
        'host' => 'localhost',
        'user' => 'admin_simpleapp1',
        'password' => 'simpleapp1',
        'dbname' => 'admin_simpleapp1'
    ]
];
// trasform to object
$config = json_decode(json_encode($config));
