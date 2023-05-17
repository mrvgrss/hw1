<?php
/*

TYPE = add TRY ADD
TYPE = remove/else TRY REMOVE

CITY = name of city ONLY CITY ON 'CITIES' LIST

*/

define('CITIES', array("Roma", "Dublino", "Vienna", "NewYork", "Miami", "Napoli"));

include '../dbconfig.php';
session_start();
$response = array("status" => "error");
if(!isset($_SESSION["email"]) || !isset($_GET["type"]) || !isset($_GET["city"]) || !in_array($_GET["city"], CITIES)){
    echo json_encode($response);
    exit;
}
$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
$email = $_SESSION["email"];
$city = $_GET["city"];
$type = $_GET["type"];
if($type == "add"){
    $query = "SELECT * FROM FAVOURITE WHERE userId = (SELECT id FROM USERS WHERE email='$email') AND city = '{$city}'";
    $res = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($res) == 0) {
        $query = "INSERT INTO FAVOURITE (userId, city) VALUES ((SELECT id FROM USERS WHERE email='$email'), '{$city}')";
        $res = mysqli_query($conn, $query);
        
        if ($res) {
            $response["status"] = "completed";
        }
    }

}else{
    $query = "DELETE FROM FAVOURITE WHERE userId = (SELECT id FROM USERS WHERE email='$email') AND city = '{$city}'";
    $res = mysqli_query($conn, $query);
    if ($res) {
        $response["status"] = "completed";
    }
}
echo json_encode($response);
?>