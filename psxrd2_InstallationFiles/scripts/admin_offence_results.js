// javascript for fetching the results offence search.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting the user name from session storage for authentication
    // and forming a json.
    const name = sessionStorage.getItem('officer');
    const loginData = {
        name: name
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/admin_offence_search.php', true)
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
                const responseJson = request.responseText
                const response = JSON.parse(responseJson)

                handleError()
            }
        }
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(search) {
        const officerName = document.getElementById('test')
        officerName.innerHTML = search
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError() {
        window.location.href = "error.html";
    }
}