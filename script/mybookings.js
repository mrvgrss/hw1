const FLIGHT_BACKUP = document.querySelector('#book-flight-1').cloneNode(true);
const HEADERS = {
    "Content-Type": "application/x-www-form-urlencoded",
    "Accept-Encoding": "gzip, deflate, br",
    "Connection": "keep-alive"
}
function onResponse(response){
    return response.json();
}
function onFlightJson(json){
    console.log(json);
    const mybookings = document.querySelector("#mybookings");
    const nobookfound = document.querySelector("#nobookfound");
    const loadings = document.querySelector("#loadings");
    loadings.style.display = "none";
    mybookings.querySelectorAll(".book-flight").forEach(function(element){
        element.remove();
    });
    if(json["status"] == "error"){
        console.log("nobookfound");
        nobookfound.style.display = "block";
    }
    if(json["status"] == "completed"){
        json["data"].forEach(function(element) {
            const flight_doc = FLIGHT_BACKUP.cloneNode(true);
            flight_doc.querySelector('.idbook').textContent = element["id"];
            flight_doc.querySelector('.departureAirport').textContent = element["origin"];
            flight_doc.querySelector('.returnAirport').textContent = element["destination"];
            flight_doc.querySelector('.departureDate').textContent = element["departureDate"];
            flight_doc.querySelector('.returnDate').textContent = element["returnDate"];

            mybookings.appendChild(flight_doc);
            mybookings.style.display = "block";
        });
    }
}
function onFlight(){
    console.log("fetch flight");
    fetch("./listofbooking.php", {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(onFlightJson);
}

onFlight();