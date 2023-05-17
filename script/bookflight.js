const HEADERS = {
    "Content-Type": "application/x-www-form-urlencoded",
    "Accept-Encoding": "gzip, deflate, br",
    "Connection": "keep-alive"
}
function bookFlight(event){
    event.preventDefault();
    onBookFlight( event.currentTarget.dataset.offertId);
}
function onBookFlight(offertId){
    const parametersQuery = `offertId=${offertId}`;
    console.log("fetch book flight");
    fetch("./bookflight.php?" + parametersQuery, {
        method: "get",
        headers: HEADERS,
    }).then(onResponse).then(onJsonReviews);
}
function onResponse(response){
    return response.json();
}
function onJsonReviews(json){
    console.log(json);
    if(json["status"] != "completed"){
        return; // HANDLE ERRORS
    }
    document.querySelector("#resumeOffert").style.display = "none";
    document.querySelector("#completedBook").style.display = "block";
}

const bookFlightButton = document.querySelector('#bookFlightButton');
bookFlightButton.addEventListener('click', bookFlight);
