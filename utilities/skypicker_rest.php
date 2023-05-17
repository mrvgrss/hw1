<?php

// SKYPICKER API

define('ENDPOINT_SP', 'https://api.skypicker.com');
define('HEADERS_SP', array(
    "Content-Type: application/x-www-form-urlencoded",
    "Accept: */*",
    "Connection: keep-alive",
));

function getInfoByTermJSON($term){
    $data = http_build_query(array(
        "term" => $term,
        "location_types" => "airport",
        "active_only" => "true",
        "limit" => 1,
    ));
    $url = ENDPOINT_SP . "/locations?" . $data;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, HEADERS_SP);

    $response = curl_exec($curl);
    $result = json_decode($response, true);

    curl_close($curl);

    return $result;
}
function getIATACode($response){
    if($response["results_retrieved"] == 0){
        return "MEX";
    }

    return $response["locations"][0]["code"];

}
function getCityName($response){
    if($response["results_retrieved"] == 0){
        return "Mexico City";
    }

    return $response["locations"][0]["city"]["name"];
}
function getCountryName($response){
    if($response["results_retrieved"] == 0){
        return "Mexico";
    }
    return $response["locations"][0]["city"]["country"]["name"];
}

?>