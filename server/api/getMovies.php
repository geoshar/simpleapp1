<?php
// get all movies
$movies = $db->query("select * from movies limit 0,10")->fetch_all(MYSQLI_ASSOC);

$sql = $movies;