<?php

include  'dbconfig.php';
session_start();


if(!isset($_SESSION["email"])){
    header("Location: signup.php");
    exit;
}

if(!isset($_GET["offertId"])){
    header("Location: home.php");
    exit;
}
$offertId = $_GET["offertId"];
$email = $_SESSION["email"];
$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
$query = "SELECT * FROM FLIGHT_OFFERTS WHERE id = $offertId";
$res = mysqli_query($conn, $query);
$response = array("status" => "failed");
if(mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_object($res);
    if($row->bookedUserId == null && strtotime( $row->last_ticketing_datetime) >= strtotime(date('Y-m-d'))){
        $query = "UPDATE FLIGHT_OFFERTS
        SET bookedUserId = (SELECT id FROM USERS WHERE email = '$email')
        WHERE id = $offertId";
        $res = mysqli_query($conn, $query);
    }else{
        echo json_encode($response);
        exit;
    }
    $response["status"] = "completed";
    echo json_encode($response);
    exit;
}

echo json_encode($response);


?>