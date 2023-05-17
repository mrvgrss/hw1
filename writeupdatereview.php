<?php

include 'dbconfig.php';

session_start();


if(!isset($_SESSION["email"])){
    exit;
}

$email = $_SESSION["email"];

if(!isset($_GET["title"]) || !isset($_GET["details"]) || !isset($_GET["stars"])){
    echo 'missing parameter error';
    exit;
}
$title = $_GET["title"];
$details = $_GET["details"];
$stars = $_GET["stars"];

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}


$query = "SELECT title, details, stars FROM REVIEWS WHERE REVIEWS.userId = (SELECT id from USERS where USERS.email = '$email')";
$res = mysqli_query($conn, $query);
if(mysqli_num_rows($res) > 0){
    $query = "UPDATE REVIEWS
    SET title = '$title', details = '$details', stars = $stars
    WHERE userId = (SELECT id FROM USERS WHERE email = '$email')";

    $res = mysqli_query($conn, $query);
    print_r($res);
}else{
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO REVIEWS (title, details, stars, userId, date_creation) VALUES ('$title', '$details', $stars, (SELECT id FROM USERS WHERE email='$email'), '$date')";
    $res = mysqli_query($conn, $query);
}

header("Location: profile.php");
?>