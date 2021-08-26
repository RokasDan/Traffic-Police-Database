// javascript used in the page of report results to show details of a specific reports.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting report value link parameter to be used in the search of full report details.
    const params = new URLSearchParams(document.location.search.substring(1));
    const person = params.get("reports");

    // Getting users name for authentication purposes.
    const name = sessionStorage.getItem('officer');

    // Forming json data for later use in an mysql query
    // to find the details about a specific person.
    const loginData = {
        search: person,
        name: name
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/Areport_search.php', true)
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request.send(JSON.stringify(loginData))

    // Defining if statements if a specific response is received.
    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            // If request is successfully
            if (request.status === 200) {

                const responseJson = request.responseText
                const response = JSON.parse(responseJson)
                console.log(response.search)
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