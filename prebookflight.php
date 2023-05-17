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

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
$query = "SELECT * FROM FLIGHT_OFFERTS WHERE id = $offertId";
$res = mysqli_query($conn, $query);
$offert = null;
if(mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_object($res);
    if($row->bookedUserId == null && strtotime( $row->last_ticketing_datetime) >= strtotime(date('Y-m-d'))){
        $offert = get_object_vars($row);
    }else{
        // redirect home if offert is already booked or too late for book;
        header("Location: home.php");
        exit;
    }
}else{
    header("Location: home.php");
    exit;
}

?>
<html>
    <head>
        <title>Book flight</title>
        <link rel="stylesheet" type="text/css" href="./style/bookflight.css">
        <script src="./script/bookflight.js" defer></script>
    </head>
    <body>
        <h1>
            Prenota volo
        </h1>
        <div id="resumeOffert">
            <h3>Offerta n: <?php echo $offert["id"];?></h3>
            <div id="infoOffert">
                <div id="dividedInfo">
                    <div>Partenza: <span class="valueInfo"><?php echo $offert["origin"]; ?></span></div>
                    <div> Destinazione: <span class="valueInfo"><?php echo $offert["destination"]; ?></span></div>    
                    <div>Data volo di partenza: <span class="valueInfo"><?php echo $offert["departureDate"]; ?></span></div>
                    <div>Data volo di ritorno: <span class="valueInfo"><?php echo $offert["returnDate"]; ?></span></div>                   
                </div>
                <div>
                    <div id="priceOffert">
                        <div class="info">
                            <span class="valueInfo">Prezzo di prenotazione</span>
                        </div>
                        <div class="info">
                            <span>Passeggeri <span class="valueInfo"><?php echo $offert["adults"]; ?>x</span></span>
                            
                        </div>
                        <div class="info subtotal">
                            <span>Prezzo base </span>
                            <span class="valueInfo"><?php echo $offert["base_price"]; ?> EUR</span>
                        </div>
                        <div class="info total">
                            <span>Totale</span>
                            <span class="valueInfo"><?php echo $offert["price"]; ?> EUR</span>
                        </div>
                    </div>
                    <div id="bookoffert">
                        <div id="backHome" class="button">
                            <a href="./home.php">Indietro</a>
                        </div>
                        <div id="bookFlightButton" class="button" data-offert-id='<?php echo $offert["id"];?>'>
                            <span>Prenota volo</span>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div id="completedBook">
            <h1>Prenotazione completata con successo!</h1>
            <p>
                Eccellente! La tua prenotazione è stata completata con successo. Grazie per aver scelto il nostro servizio di prenotazione online.
                <br>
                Per visualizzare la tua prenotazione, puoi trovare un elenco completo delle tue prenotazioni nella sezione "Le mie prenotazioni" del nostro sito web. Basta cliccare <a href="mybookings.php">qui</a> per andare direttamente alla pagina delle tue prenotazioni.
                <br>
                Ancora grazie per aver scelto il nostro servizio di prenotazione online. Ci auguriamo che tu abbia un ottimo viaggio!
            </p>
        </div>
    </body>
</html>