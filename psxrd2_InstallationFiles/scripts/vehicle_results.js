// javascript used in the page of vehicle results to show details of a specific vehicle.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting vehicle value from link parameter to be used in the vehicle details search
    const params = new URLSearchParams(document.location.search.substring(1));
    const cars = params.get("cars");

    // Getting users name for authentication purposes.
    const name = sessionStorage.getItem('officer');

    // Forming json data for later use in an mysql query
    // to find the details about a specific vehicle.
    const loginData = {
        search: cars,
        name: name
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/vehicle_results.php', true)
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request.send(JSON.stringify(loginData))

    // Defining if statements if a specific response is received.
    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            // If request is successfully
            if (request.status === 200) {

                const responseJson = request.responseText
                const response = JSON.parse(responseJson)
                handleSuccess(response.search)

                // If request is unsuccessfully

            } else {

                handleError()
            }
        }
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(search) {
        const errorMessage = document.getElementById('result')
        errorMessage.innerHTML = search
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError() {
        window.location.href = 'error.html';
    }
}