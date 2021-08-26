// javascript used in the page of vehicle search to send the search key to the mysql query.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(search, name, onSuccess, onError) {
        const loginData = {
            search: search,
            name: name
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/officer_vehicle_search.php', true)
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

                    onSuccess(response.search)

                    // If request is unsuccessfully
                } else {

                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onError(response.error)
                }
            }
        }
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(search) {
        sessionStorage.setItem("cars", search);
        window.location.href = "officer_vehicle_results.html";
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an function which will be activated once a form submission is received.
    function handleLogin(event) {
        event.preventDefault()

        const search = form.querySelector('[name=search]').value
        const name = sessionStorage.getItem('officer');

        login(search, name, handleSuccess, handleError)
    }

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', handleLogin)
}