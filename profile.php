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
$query = "SELECT * from USERS where USERS.email = '$email'";
$res = mysqli_query($conn, $query);
$name = null;
$surname = null;
if(mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_object($res);
    $name = $row->name;
    $surname = $row->surname;
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
        <meta name="viewport"
        content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="./style/profile.css">
    </head>
    <body>
        <div id="profileinfo" class="moduleinfo">
            <div id="headerinfo">
                <span>
                    My Account
                </span>
                <span>
                    <?php echo $email ?>
                </span>
            </div>
            <div id="detailsinfo">
                <form>
                    <label>First name:</label>
                    <input type="text" placeholder="First name" value=<?php echo $name ?>>
                    <label>Last name:</label>
                    <input type="text" placeholder="Last name" value=<?php echo $surname ?>>

                </form>
                <div class="savechange" style="display: none;">
                    <div>
                        <span>Salva le modifiche</span>
                    </div> 
                </div>
            </div>
        </div>
        <div class="moduleinfo">
            <h3>Scrivi recensione</h3>
            <form method="get" action="writeupdatereview.php">
                <label>Titolo:</label>
                <input type="text" name="title" value="<?php echo $title ?>" placeholder="Titolo">
                <label>Commento:</label>
                <input type="text" name="details" value="<?php echo $details ?>" placeholder="Commento">
                <label>Stelle:</label>
                <input type="number" name="stars" min="1" max="5" value="<?php echo $stars ?>" placeholder="Stelle">
                <input type="submit" value="<?php echo $button ?>">
                
            </form>
        </div>
    </body>
</html>