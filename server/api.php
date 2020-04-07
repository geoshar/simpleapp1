<?php
// connect to database
require_once('db/db.php');

$apiAvailable = [
    'getMovies'
];

// check if there's a request
if ($_REQUEST) {
    // check if the request match available api methods
    $method = array_keys($_REQUEST)[0];
    if (in_array($method, $apiAvailable)) {
        require_once('api/' . $method . '.php');

        echo json_encode($sql, true);
    }

}