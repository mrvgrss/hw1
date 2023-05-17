<?php
include './utilities/auth.php';
include 'dbconfig.php';
session_start();

if(isset($_SESSION["email"])){
    header("Location: home.php");
    exit;
}
if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["name"]) && isset($_POST["surname"])){
    $conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
    if(!$conn){
        die('Error database connection: ' . mysqli_connect_error());
    }
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid mail address";
        exit;
    }
    $password =  mysqli_real_escape_string($conn, $_POST["password"]);

    $password_hash = hashPassword($password); 

    $query = "SELECT * FROM users WHERE email = '{$email}'";
    $res = mysqli_query($conn, $query);

    if(mysqli_num_rows($res) > 0){
        $error = "This email has been already used";
        exit;
    }
    $query = "INSERT into USERS(email, password, name, surname) VALUES('{$email}', '{$password_hash}', '{$name}', '{$surname}')";
    $res = mysqli_query($conn, $query);
    if($res){
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;
        header("Location: home.php");
        exit;
    }else{
        $error = "Error database insertion";
    }
}

?>
<html>
    <head>
        <title>signup</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="./style/authstyle.css">
        <meta name="viewport"
        content="width=device-width, initial-scale=1">
        <script src="./script/passwordview.js" defer></script>
        <script src="./script/signup.js" defer></script>
    </head>
    <body>
        <div id="authlayout">
            <div id="formauthlayout">
                <form name='input_form' method='post'>
                    <div class="rowinput">
                        <div class="forminput">
                            <input type='text' name='name' placeholder="Nome">
                            <span>Nome inserito invalido</span>
                        </div>
                        <div class="forminput">
                            <input type='text' name='surname' placeholder="Cognome">
                            <span>Cognome inserito invalido</span>
                        </div>
                        
                    </div>
                    <div class="forminput">
                        <input type='text' name='email' placeholder="Email">
                        <span>Email inserita non valida</span>
                    </div>
                    <div class="forminput">
                        <div class="passwordview hideicon"></div>
                        <input type='password' name='password' placeholder="Password">
                        <span>Password inserita non valida</span>
                    </div>
                    <div class="forminput">
                        <div class="passwordview hideicon"></div>
                        <input type='password' name='confirmpassword' placeholder="Conferma Password">
                        <span>Password non corrisponde</span>
                    </div>
                    <div class="forminput">
                        <input type='submit' id="submit" value="Registrati">
                        <span>Invio non riuscito</span>
                    </div>
                </form>
            </div>
            <span>Hai già un account? Entra <a href="login.php">qui</a></span>
        </div>
    </body>
</html>