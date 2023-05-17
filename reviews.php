<?php

require 'dbconfig.php';

if(!isset($_GET["max"])){
    echo 'missing parameter error';
    exit;
}

$max = $_GET["max"];

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}

$query = "SELECT title, date_creation, name, surname, stars, details from REVIEWS, USERS where REVIEWS.userId = USERS.id ORDER BY date_creation DESC LIMIT $max";
$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) > 0){
    echo json_encode(array("status" => "completed", "data" => mysqli_fetch_all($res, MYSQLI_ASSOC)));
    exit;
}

echo json_encode(array("status" => "noresult"));

?>
