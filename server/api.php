<?php
// connect to database
require_once('db/db.php');
// connect libraries
require_once('libs/htmlParser.php');
$apiAvailable = [
    'movies'
];
// decode the encoded post string
$_POST = json_decode(array_keys($_POST)[0], true);
$api = explode('/', $_POST['api']);
$method = $api[0];
$action = $api[1];
// check if the request match available api methods
if (in_array($method, $apiAvailable)) {

    require_once('api/' . $method . '.php');
    $methodClass = new $method();
    // check if the action exists, if not, return message
    if (method_exists($methodClass, $action)) {
        // assign db to a class
        $methodClass->db = $db;

        // get action
        $output = $methodClass->$action();
        // output result
        echo json_encode($output, true);
    } else {
        echo json_encode(['message' => 'no action: ' . $action . ''], true);
    }
}
