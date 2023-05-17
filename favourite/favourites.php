<?php

include '../dbconfig.php';
session_start();
$response = array("status" => "error", "data" => null);
if(!isset($_SESSION["email"])){
    $response["status"] = "sessionexpired";
    echo json_encode($response);
    exit;
}
$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
$email = $_SESSION["email"];

$query = "SELECT city FROM FAVOURITE WHERE userId= (SELECT id from USERS where USERS.email = '$email')";
$res = mysqli_query($conn, $query);
if(mysqli_num_rows($res) > 0){
    $response["status"] = "completed";
    $response["data"] = array_column(mysqli_fetch_all($res), 0);
}
echo json_encode($response);
?>