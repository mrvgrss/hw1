<?php

require 'dbconfig.php';

$title = "";
$details = "";
$stars = 5;
$button = "Scrivi";
session_start();
if(!isset($_SESSION["email"])){
    header("Location: login.php");
    exit;
}

$email = $_SESSION["email"];

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}


$query = "SELECT title, details, stars FROM REVIEWS WHERE REVIEWS.userId = (SELECT id from USERS where USERS.email = '$email')";
$res = mysqli_query($conn, $query);
if(mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_object($res);
    $title = $row->title;
    $details = $row->details;
    $stars = $row->stars;
    $button = "Aggiorna";
}
?>


<html>
    <head>
        <title>Profile</title>
    </head>
    <body>
        <h1>Scrivi recensione</h1>
        <form method="get" action="writeupdatereview.php">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo $title ?>">
            <label>Commento:</label>
            <input type="text" name="details" value="<?php echo $details ?>">
            <label>Stars:</label>
            <input type="number" name="stars" min="1" max="5" value="<?php echo $stars ?>">
            <label></label>
            <input type="submit" value="<?php echo $button ?>">
        </form>
    </body>
</html>