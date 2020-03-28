<?php
// Database connection
$db = [
    'host' => 'localhost',
    'user' => 'local',
    'password' => '123',
    'dbname' => 'simpleapp1'
];
// Create connection
$conn = new mysqli($db['host'], $db['host'], $db['host'], $db['dbname']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
