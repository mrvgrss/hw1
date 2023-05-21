<?php

include './dbconfig.php';

session_start();

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
if(!isset($_SESSION["email"])){
    header("Location: login.php");
    exit;
}

$email = $_SESSION["email"];
$response = array("status" => "error", "data" => null);
$query = "SELECT * from FLIGHT_OFFERTS WHERE bookedUserId = (SELECT id from USERS where USERS.email = '$email')";
$res = mysqli_query($conn, $query);
$data = array();
if(mysqli_num_rows($res) > 0){
    while($row = mysqli_fetch_assoc($res))
    {
          $data[] = $row;
    }
    $response["data"] = $data;
    $response["status"] = "completed";
}

echo json_encode($response);




?>