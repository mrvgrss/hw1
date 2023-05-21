<html>
    <head>
        <title>MyBookings</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="./style/mybookings.css">
        <script src="./script/mybookings.js" defer></script>
    </head>
    <body>
        <h1>Le mie prenotazioni</h1>
        <div id="mybookings">
            <div class="book-flight" id="book-flight-1">
                <h3>Prenotazione volo n: <span class="idbook">1</span> </h3>
                <div class="itineraries">
                    <div class="itinerary">
                        <span>Volo andata:</span>
                        <span class="departureAirport">FCO</span>
                        <span class="departureDate">2023-07-06</span>
                    </div>
                    <div class="itinerary">
                        <span>Volo ritorno:</span>
                        <span class="returnAirport">MEX</span>
                        <span class="returnDate">2023-07-10</span>
                    </div>
                </div>
                
            </div>
        </div>
        <div id="loadings">
            <span>Caricamento...</span>
        </div>
        <div id="nobookfound">
            <span>Nessuna prenotazione trovata</span>
        </div>
        <div id="returnHomepage">
            <div>
                <a href="home.php">Ritorna al home page</a>
            </div>
        </div>
    </body>
</html>
