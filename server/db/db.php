<?php
// Database connection
$db_ = [
    'host' => 'localhost',
    'user' => 'admin_simpleapp1',
    'password' => 'simpleapp1',
    'dbname' => 'admin_simpleapp1'
];
// Create connection
$db = new mysqli($db_['host'], $db_['user'], $db_['password'], $db_['dbname']);
// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
