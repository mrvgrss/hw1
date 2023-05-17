<?php
require 'dbconfig.php';
require './utilities/auth.php';

session_start();
if(isset($_SESSION["email"])){
    header("Location: home.php");
    exit;
}

if(isset($_POST["email"]) && isset($_POST["password"])){
    $conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
    if(!$conn){
        die('Error database connection: ' . mysqli_connect_error());
    }
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password =  mysqli_real_escape_string($conn, $_POST["password"]);
    // https://www.php.net/manual/en/function.password-hash.php
    $password_hash = hashPassword($password);

    $query = "SELECT * FROM users WHERE email = '{$email}'";
    $res = mysqli_query($conn, $query);

    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_object($res);
        print_r($row);
        if(password_verify($password, $row->password)){
            $_SESSION["email"] = $_POST["email"];
            $_SESSION["name"] = $row->name;
            header("Location: home.php");
        }
        exit;
    }else{
        $error = 'Nessun utente trovato o password inserita non correttamente!';
    }

}
?>
<html>
    <head>
        <title>login</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="./style/authstyle.css">
        <meta name="viewport"
        content="width=device-width, initial-scale=1">       
    </head>
    <body>
        <div id="authlayout">
            <div id="formauthlayout">
                <form name='input_form' method='post'>
                    <div class="forminput"> <!-- FLEXIBLE LAYOUT BASED ON AUTHSTYLE CSS COMPATIBLE WITH SIGNUP PAGE-->
                        <input type='text' name='email' placeholder="Email">
                    </div>
                    <div class="forminput">
                        <input type='password' name='password' placeholder="Password">
                    </div>
                    <div class="forminput">
                        <input type='submit' id="submit" value="Entra">
                    </div>
                </form>
            </div>
        </div>
        <?php
            if(isset($error)){
                echo $error;
            }
        ?>
    </body>
</html>