<?php
require 'dbconfig.php';
require './utilities/skypicker_rest.php';
require './utilities/amadeus_rest.php';
function checkCode($conn, $term){
    $code = null;
    $query = "SELECT * FROM AIRPORTS WHERE city = '{$term}' OR iata = '{$term}'";
    $res = mysqli_query($conn, $query);
    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_object($res);
        $code = $row->iata;
    }else{
        $data = getInfoByTermJson($term);
        $code = getIATACode($data);
        $query = "SELECT * FROM AIRPORTS WHERE iata = '{$code}'";
        $res = mysqli_query($conn, $query);
        if(mysqli_num_rows($res) > 0){
            return $code;
        }
        $city = getCityName($data);
        $country = getCountryName($data);
        $query = "INSERT into AIRPORTS(iata, city, country) VALUES ('{$code}', '{$city}', '{$country}')";
        $res = mysqli_query($conn, $query);
    }

    return $code;
}
function checkCachedOfferts($conn, $origin, $destination, $departureDate, $returnDate, $adults){
    $offerts = array();
    // returnDate is not used on cached offerts search.
    $query = "SELECT *, DATEDIFF(departureDate, '{$departureDate}') AS date_diff FROM FLIGHT_OFFERTS WHERE 
      bookedUserId IS NULL AND
      destination = '{$destination}' AND
      origin = '{$origin}' AND 
      adults = '{$adults}' AND
      departureDate >= '{$departureDate}' AND
      last_ticketing_datetime >= '{$departureDate}'
      ORDER BY date_diff ASC
      LIMIT 5
      ";
    $res = mysqli_query($conn, $query);
    if(mysqli_num_rows($res) > 0){
        $i = 0;
        while($row = mysqli_fetch_object($res)){
            $offertId = $row->id;
            $offerts[$i]["info"] = get_object_vars($row);
            $query = "SELECT * from FLIGHTS WHERE FLIGHTS.offertId = '{$offertId}'";
            $resFlights = mysqli_query($conn, $query);
            $k = 0;
            while($rowFlight = mysqli_fetch_object($resFlights)){
                $outbound = $rowFlight->outbound;
                $offerts[$i]["flights"][$k] = get_object_vars($rowFlight);
                $query = "SELECT * FROM SEGMENTS WHERE SEGMENTS.offertId = '{$offertId}' AND SEGMENTS.outbound = '{$outbound}'";
                $resSegments = mysqli_query($conn, $query);
                while($rowSegment = mysqli_fetch_object($resSegments)){
                    $offerts[$i]["flights"][$k]["segments"][] = get_object_vars($rowSegment);
                }
                $k++;
            }
            $i++;
        }
        
    }
    return $offerts;
}
function extractOfferts($dataJSON){
    $offerts = array();
    $dictionary = $dataJSON["dictionaries"];
    foreach($dataJSON["data"] as $key => $value){
        $offert = array();
        $offert["info"]["source_offert"] = $value["source"];
        $offert["info"]["oneway"] = $value["oneWay"];
        $offert["info"]["last_ticketing_datetime"] = $value["lastTicketingDateTime"];
        $offert["info"]["price"] = $value["price"]["total"];
        $offert["info"]["base_price"] = $value["price"]["base"];
        $offert["info"]["genereted"] = time();
        $offert["info"]["origin"] = $value["itineraries"][0]["segments"][0]["departure"]["iataCode"];
        $offert["info"]["destination"] = $value["itineraries"][0]["segments"][count($value["itineraries"][0]["segments"]) - 1]["arrival"]["iataCode"];
        $offert["info"]["departureDate"] = $value["itineraries"][0]["segments"][0]["departure"]["at"];
        $offert["info"]["returnDate"] = $value["itineraries"][1]["segments"][count($value["itineraries"][0]["segments"]) - 1]["arrival"]["at"];
        $offert["info"]["adults"] = count($value["travelerPricings"]);
        foreach($value["itineraries"] as $key1 => $value1){
            $offert["flights"][$key1]["duration"] = $value1["duration"];
            foreach($value1["segments"] as $key2 => $value2){
                $offert["flights"][$key1]["segments"][$key2] = array(
                    "segment_n" => $key2,
                    "duration" => $value2["duration"],
                    "departure_airport" => $value2["departure"]["iataCode"],
                    "departure_terminal" => isset($value2["departure"]["terminal"]) ? $value2["departure"]["terminal"] : 0,
                    "departure_datetime" => $value2["departure"]["at"],
                    "arrival_airport" => $value2["arrival"]["iataCode"],
                    "arrival_datetime" => $value2["arrival"]["at"],
                    "company_name" => $dictionary["carriers"][$value2["carrierCode"]],
                    "aircraft" => $dictionary["aircraft"][$value2["aircraft"]["code"]],
                );
            } 
        }
        $offerts[$key] = $offert;
    }
    return $offerts;
}
function addOfferts($conn, $offerts){
    foreach($offerts as $key => $value){
        $query = "INSERT INTO FLIGHT_OFFERTS (" . implode(", ", array_keys($value["info"])) . ") VALUES ('" . implode("', '", $value["info"]) . "')";
        $res = mysqli_query($conn, $query);
        $id = mysqli_insert_id($conn);
        foreach($value["flights"] as $key1 => $value1){
            $duration = $value1["duration"];
            $query = "INSERT INTO FLIGHTS(offertId, outbound, duration) VALUES ($id, $key1, '$duration')";
            $res = mysqli_query($conn, $query);
            foreach($value1["segments"] as $key2 => $value2){
                $value2["offertId"] = $id;
                $value2["outbound"] = $key1;
                $query = "INSERT INTO SEGMENTS (" . implode(", ", array_keys($value2)) . ") VALUES ('" . implode("', '", $value2) . "')";
                $res = mysqli_query($conn, $query);
            }
        }

    }
}
// origin*
// destination*
// departureDate
// returnDate
// adults
// max

if(!isset($_GET["origin"]) || !isset($_GET["destination"])){
    echo 'missing parameter error';
    exit;
}

$origin = $_GET["origin"];
$origin_code = null;
$destination = $_GET["destination"];
$destination_code = null;
$departureDate = null;
$returnDate = null;
$adults = 1;
$max = 1;

$conn = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
if(!$conn){
    die('Error database connection: ' . mysqli_connect_error());
}
$origin_code = checkCode($conn, $origin);
$destination_code = checkCode($conn, $destination);

if(!isset($_GET["departureDate"])){
    $departureDate = (new DateTime())->format('Y-m-d');
}else{
    $departureDate = $_GET["departureDate"];
}
if(!isset($_GET["returnDate"]) || $_GET["returnDate"] < $departureDate){
    $returnDate = (new DateTime($departureDate))->modify('+2 days')->format('Y-m-d');
}else{
    $returnDate = $_GET["returnDate"];
}
if(isset($_GET["adults"]) && $_GET["adults"] <= 5){
    $adults = $_GET["adults"];
}
if(isset($_GET["max"])){
    $max = $_GET["max"];
}
$offerts = checkCachedOfferts($conn, $origin_code, $destination_code, $departureDate, $returnDate, $adults);
if(empty($offerts)){
    $tokenInfo = getToken();
    $flightOffertsJSON = getFlightOfferts($tokenInfo[0], $tokenInfo[1], $origin_code, $destination_code, $departureDate, $returnDate, $adults, $max);
    if(isset($flightOffertsJSON["data"]) && count($flightOffertsJSON["data"]) > 0){
        $extracted = extractOfferts($flightOffertsJSON);
        $err = addOfferts($conn, $extracted); // do something in case of error
        $offerts = checkCachedOfferts($conn, $origin_code, $destination_code, $departureDate, $returnDate, $adults);
    }
}

mysqli_close($conn);

echo json_encode($offerts);

?>