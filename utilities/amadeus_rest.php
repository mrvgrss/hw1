<?php

// AMADEUS API
// SELF SERVICE(LIMITED CALLS)
// DOC https://developers.amadeus.com/self-service/apis-docs

define('ENDPOINT', 'https://test.api.amadeus.com');
define('PUBLIC_KEY', 'rbdKT4s9iUY9PoGJoXtAecow3S5nj9rm');
define('PRIVATE_KEY', 'ZJpTzCN60eZciGHo');
define('HEADERS', array(
    "Content-Type: application/x-www-form-urlencoded",
    "Accept: */*",
    "Connection: keep-alive"
));

function getToken(){
    $url = ENDPOINT . "/v1/security/oauth2/token";
    $data = array(
        'grant_type' => 'client_credentials',
        'client_id' => PUBLIC_KEY,
        'client_secret' => PRIVATE_KEY
    );
    $postfields = http_build_query($data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, HEADERS);

    $response = curl_exec($curl);
    $result = json_decode($response);

    curl_close($curl);

    return [$result->access_token, $result->token_type];

} 
function getFlightOfferts($accessToken, $tokenType, $originLocationCode = "ROM", $destinationLocationCode = "MEX", $departureDate = "2023-07-19", $returnDate = "2023-07-29", $adults = 1, $max = 1){
    $data = http_build_query(
        array(
            "originLocationCode" => $originLocationCode,
            "destinationLocationCode" => $destinationLocationCode,
            "departureDate" => $departureDate,
            "returnDate" => $returnDate,
            "adults" => $adults,
            "max" => $max
        )
    );
    $url = ENDPOINT . "/v2/shopping/flight-offers?" . $data;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $authHeader = array("Authorization: {$tokenType} {$accessToken}");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(HEADERS, $authHeader));

    $response = curl_exec($curl);
    $result  = json_decode($response, true);
    curl_close($curl);

    return $result;
}
// $tokenInfo = getToken(); debug
?>