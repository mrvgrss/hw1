const HEADERS = {
    "Content-Type": "application/x-www-form-urlencoded",
    "Accept-Encoding": "gzip, deflate, br",
    "Connection": "keep-alive"
}
// HTML RESULT BACKUP
const OFFERT_BACKUP = document.querySelector('#flight-offert-1').cloneNode(true);
const REVIEW_BACKUP = document.querySelector('#review-1').cloneNode(true);

function onResponse(response){
    return response.json();
}
function convertDateIfNull(dateString, addDays = 1) {
    if (!dateString) {
      let today = new Date();
      today.setDate(today.getDate() + addDays);
      return today.toISOString().slice(0, 10);
    }
    return dateString;
}
function formatDuration(duration) {
    const hoursIndex = duration.indexOf('H');
    const minutesIndex = duration.indexOf('M');
    const hours = hoursIndex > 0 ? parseInt(duration.substring(2, hoursIndex)) : 0;
    const minutes = minutesIndex > 0 ? parseInt(duration.substring(hoursIndex + 1, minutesIndex)) : 0;

    const result = `${hours} ore e ${minutes} minuti`;
  
    return result;
}
function formatDateAndTime(dateTimeString) {
    
    const dateObj = new Date(dateTimeString);
  
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    const date = dateObj.toLocaleDateString('it-IT', options);
  
    const time = dateObj.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
  
    return {
      date: date,
      time: time
    };
}
function searchFlight(event) {
    event.preventDefault(); // prevent default activity of the submit button
    let flight_origin_value = encodeURIComponent(document.querySelector("#flight-origin").value);
    let flight_destination_value = encodeURIComponent(document.querySelector("#flight-destination").value);
    const flight_adults_value = encodeURIComponent(document.querySelector("#flight-adults").value);
    const flight_departure_date_value = convertDateIfNull(encodeURIComponent(document.querySelector("#flight-departure-date").value));
    const flight_return_date_value = convertDateIfNull(encodeURIComponent(document.querySelector("#flight-return-date").value), 2);

    onFlightOffert(flight_origin_value, flight_destination_value, flight_departure_date_value, flight_return_date_value, flight_adults_value, 5);
}
function onFlightOffert(originLocationCode, destinationLocationCode, departureDate, returnDate, adults, max=1){ 

    document.querySelector('#loading-cotainer').style.display = 'block'; // DISPLAY LOADING CONTAINER
    document.querySelector('#flight-offert').style.display = 'none'; // HIDE OFFERT CONTAINER

    const parametersQuery = `origin=${originLocationCode}&destination=${destinationLocationCode}&departureDate=${departureDate}&returnDate=${returnDate}&adults=${adults}&max=${max}`

    console.log("fetch");

    fetch("./flight.php?" + parametersQuery, {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(onJsonFlightOffert);
}
function onJsonFlightOffert(json){
    
    document.querySelectorAll(".flight-offert-item").forEach(function(element) {
        element.remove();
    });
    for(var k = 0; k < json.length; k++){
        let data = json[k];
        let info = data["info"];
        let flights = data["flights"]; 
        const offert_doc = OFFERT_BACKUP.cloneNode(true);
        document.querySelector('#flight-offert').appendChild(offert_doc);
        offert_doc.querySelector('.expand-details').dataset.offertId = k;
        offert_doc.querySelector('.price-button').dataset.offertId = info["id"];
        offert_doc.querySelector('.id-offer').textContent = k+1;
        offert_doc.querySelector('.price').textContent = info.price;
        offert_doc.querySelector('.price-currency').textContent = "EUR";
        offert_doc.querySelector('.flight-offert-info-company').textContent = info.airline;
        offert_doc.querySelector('.flight-start-airport').textContent = info.origin;
        offert_doc.querySelector('.flight-end-airport').textContent = info.destination;

        offert_doc.querySelector('.flight-duration').textContent = formatDuration(flights[0]["duration"]);
        const stopovers = flights[0]["segments"].length - 1;
        offert_doc.querySelector('.flight-stopovers').textContent = "(" + stopovers + "scali)";
        offert_doc.querySelector('.flight-start-hour').textContent = formatDateAndTime(flights[0]["segments"][0]["departure_datetime"]).time;
        offert_doc.querySelector('.flight-end-hour').textContent = formatDateAndTime(flights[0]["segments"][flights[0]["segments"].length - 1]["arrival_datetime"]).time;

        const planes = offert_doc.querySelector('.planes');
        const plane_stopovers_img = planes.querySelector('img');
        for(var i = 0; i < parseInt(flights[0]["segments"].length); i++){
            const image = document.createElement('img');
            image.src = plane_stopovers_img.src;

            planes.appendChild(image);
        }
        // DEPARTURE FLIGHT
        const departureFlight = offert_doc.querySelector('.departureFlight');
        let segment = departureFlight.querySelector('.segment');
        let s = departureFlight.querySelector('.segments');
        for(var i = 0; i < flights[0].segments.length - 1; i++){
            const clone = segment.cloneNode(true);
            s.appendChild(clone);
        }

        let segments = departureFlight.querySelectorAll('.segment');
        let company_name = "";
        for(var i = 0; i < flights[0].segments.length; i++){
            const seg = segments[i];
            const seg_data = flights[0].segments[i];
            seg.querySelector('.info-segment .title .at').textContent = formatDateAndTime(seg_data.departure_datetime).date;
            
            const departure = seg.querySelector('.departure');
            departure.querySelector('.hour').textContent = formatDateAndTime(seg_data.departure_datetime).time;
            departure.querySelector('.terminal').textContent = seg_data.departure_terminal;
            departure.querySelector('.airport').textContent = seg_data.departure_airport;

            const arrival = seg.querySelector('.arrival');
            arrival.querySelector('.hour').textContent = formatDateAndTime(seg_data.arrival_datetime).time;
            arrival.querySelector('.airport').textContent = seg_data.arrival_airport;

            seg.querySelector('.carrier_code').textContent = seg_data.company_name;
            seg.querySelector('.aircraft').textContent = seg_data.aircraft;
            seg.querySelector('.duration').textContent = seg_data.duration;

            company_name = seg_data.company_name;
        }
        offert_doc.querySelector('.flight-offert-info-company').textContent = company_name;
        // RETURN FLIGHT
        const returnFlight = offert_doc.querySelector('.returnFlight');

        segment = returnFlight.querySelector('.segment');
        s = returnFlight.querySelector('.segments');
        for(var i = 0; i < flights[1].segments.length - 1; i++){
            const clone = segment.cloneNode(true);
            s.appendChild(clone);
        }

        segments = returnFlight.querySelectorAll('.segment');
        for(var i = 0; i < flights[1].segments.length; i++){
            const seg = segments[i];
            const seg_data = flights[1].segments[i];
            seg.querySelector('.info-segment .title .at').textContent = formatDateAndTime(seg_data.departure_datetime).date;
            
            const departure = seg.querySelector('.departure');
            departure.querySelector('.hour').textContent = formatDateAndTime(seg_data.departure_datetime).time;
            departure.querySelector('.terminal').textContent = seg_data.departure_terminal;
            departure.querySelector('.airport').textContent = seg_data.departure_airport;

            const arrival = seg.querySelector('.arrival');
            arrival.querySelector('.hour').textContent = formatDateAndTime(seg_data.arrival_datetime).time;
            arrival.querySelector('.airport').textContent = seg_data.arrival_airport;

            seg.querySelector('.carrier_code').textContent = seg_data.company_name;
            seg.querySelector('.aircraft').textContent = seg_data.aircraft;
            seg.querySelector('.duration').textContent = seg_data.duration;
        }
    }
    document.querySelector('#loading-cotainer').style.display = 'none'; // HIDE LOADING CONTAINER
    document.querySelector('#flight-offert').style.display = 'block'; // SHOW OFFERT CONTAINER
    OffertListener();
}
function expandDetails(event){
    let offertId = event.currentTarget.dataset.offertId;
    console.log(offertId);
    const offert = document.querySelectorAll(".flight-offert-item")[parseInt(offertId)];
    const its = offert.querySelector(".itineraries");
    if(its.style.display == "block"){
        its.style.display = "none";
        event.currentTarget.classList.remove('show');
    }else{
        its.style.display = "block";
        event.currentTarget.classList.add('show');
    }
    event.stopPropagation();
}
function OffertListener(){
    var expandDetailsContainers = document.querySelectorAll('.expand-details');
    var priceButtonContainers = document.querySelectorAll('.price-button');
    expandDetailsContainers.forEach(function(container) {
        container.addEventListener('click', expandDetails);
    });
    priceButtonContainers.forEach(function(container) {
        container.addEventListener('click', priceButtonClick);
    });
}
function priceButtonClick(event){
    let offertId = event.currentTarget.dataset.offertId;
    window.location.href = "prebookflight.php?offertId=" + offertId;

}
function onFavourites(){
    console.log("fetch favourites");
    fetch("./favourite/favourites.php", {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(onJsonFavourites);
}
function onJsonFavourites(json){
    const favourites = document.querySelectorAll('.destinations-container-column-item .favourite');
    if(json["status"] == "sessionexpired"){
        favourites.forEach(element => {
            element.style.display = "none";
        });
    }
    if(json["status"] == "completed"){
        favourites.forEach(element => {
            if(json["data"].includes(element.dataset.cityName)){
                element.classList.add("selectedfavourite");
            }
        });  
    }
}
function onReviews(){
    const parametersQuery = `max=5`;
    console.log("fetch reviews");
    fetch("./reviews.php?" + parametersQuery, {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(onJsonReviews);
}
function onJsonReviews(json){
    if(json["status"] != "completed"){
        return;
    }
    data = json["data"];
    for(var k = 0; k < data.length; k++){
        let info = data[k];
        const review_doc = REVIEW_BACKUP.cloneNode(true);
        document.querySelector('#reviews-container').appendChild(review_doc);

        review_doc.querySelector(".review-title").textContent = info["title"];
        review_doc.querySelector(".review-description").textContent = info["details"];
        review_doc.querySelector(".review-author-date").textContent = info["name"] + " " + info["surname"] + ", " + formatDateAndTime(info["date_creation"]).date;
        review_doc.querySelector(".review-description").textContent = info["details"];
        
        const review_star = review_doc.querySelector('.review-row-star div');
        const review_star_img = review_star.querySelector('img');
        review_star.innerHTML = "";
        for(var i = 0; i < info["stars"]; i++){
            const image = document.createElement('img');
            image.src = review_star_img.src;

            review_star.appendChild(image);
        }
    }
}
function setFavourite(event) {
    event.preventDefault();
    const currentTarget = event.currentTarget;
    const city = currentTarget.dataset.cityName; 
    const type = currentTarget.classList.contains("selectedfavourite") ? "remove" : "add";
    const parametersQuery = `city=${city}&type=${type}`;
    console.log("fetch reviews");
    fetch("./favourite/setfavourite.php?" + parametersQuery, {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(response => {
        if(response["status"] == "completed"){
            if(type == "add"){
                currentTarget.classList.add("selectedfavourite");
            }else{
                currentTarget.classList.remove("selectedfavourite");
            }
        }
    });
}
function FavouriteListener(){
    var favourites = document.querySelectorAll('.destinations-container-column-item .favourite');
    favourites.forEach(function(fav) {
        fav.addEventListener('click', setFavourite);
    });
}
const formFlight = document.querySelector('#flight-form');
formFlight.addEventListener('submit', searchFlight);
FavouriteListener();

// LOAD STORED REVIEWS MAX 5
onReviews();
onFavourites();