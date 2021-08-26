// javascript displaying the details of a specific offence.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Receiving a value from stored in a link parameter
    const params = new URLSearchParams(document.location.search.substring(1));
    const plate = params.get("details");

    // Receiving a value stored in session storage
    const name = sessionStorage.getItem('officer');

    // creating json data for the XMLH request.
    const Data = {
        search: plate,
        name: name
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/offence_details.php', true)
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request.send(JSON.stringify(Data))

    // Defining if statements if a specific response is received.
    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            // If request is successfully
            if (request.status === 200) {

                const responseJson = request.responseText
                const response = JSON.parse(responseJson)
                handleSuccess(response.search)

                // If request is unsuccessfully.
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

    // Defining an action function which will do something on an unsuccessfully response.
    function handleError() {
        window.location.href = 'error.html';
    }
}