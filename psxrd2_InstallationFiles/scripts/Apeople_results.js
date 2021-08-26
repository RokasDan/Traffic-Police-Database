// javascript used in the page of people results to show details of a specific person.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting person value from link parameter to be used in the offenders vehicle search
    const params = new URLSearchParams(document.location.search.substring(1));
    const person = params.get("people");

    // Getting users name for authentication purposes.
    const name = sessionStorage.getItem('officer');

    // adding the person value to the link parameter
    // which will be used to find the specific offenders vehicles.
    const carsbutton = document.getElementById('owner')
    const link = "admin_person_car_check.html?owner=" + person
    carsbutton.setAttribute('href', link);

    // Forming json data for later use in an mysql query
    // to find the details about a specific person.
    const loginData = {
        search: person,
        name: name
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/people_search.php', true)
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request.send(JSON.stringify(loginData))

    // Defining if statements if a specific response is received.
    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            // If request is successfully
            if (request.status === 200) {

                const responseJson = request.responseText
                const response = JSON.parse(responseJson)
                handleSuccess(response.search, response.search1)

                // If request is unsuccessfully
            } else {
                handleError()
            }
        }
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(search, search1) {
        const errorMessage = document.getElementById('result')
        const errorMessage1 = document.getElementById('carcount')
        errorMessage.innerHTML = search
        errorMessage1.innerHTML = search1
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError() {
        window.location.href = 'error.html';
    }
}