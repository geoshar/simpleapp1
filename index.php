<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 28/03/2020
 * Time: 5:06:51 PM
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// check if it's a post request
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once('server/api.php');
}else{
// then connect html main file
    require_once('client/view/index.html');
}