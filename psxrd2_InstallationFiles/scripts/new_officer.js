// javascript for creating new officer for the database.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function officer(username, password, onSuccess, onError) {

        // creating json data for the XMLH request.
        const loginData = {
            username: username,
            password: password
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/new_officer.php', true)
        request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
        request.send(JSON.stringify(loginData))

        // Defining if statements if a specific response is received.
        request.onreadystatechange = function () {
            if (request.readyState === 4) {

                // If request is successfully
                if (request.status === 200) {
                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onSuccess(response.error)

                    // If request is unsuccessfully.
                } else {

                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onError(response.error)
                }
            }
        }
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(message) {
        // store secret somewhere
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an action function which will do something on an unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an function which will be activated once a form submission is received.
    function newOfficer(event) {
        event.preventDefault()

        const username = form.querySelector('[name=username]').value
        const password = form.querySelector('[name=password]').value

        officer(username, password, handleSuccess, handleError)
    }

    // Making an event listener which will wait for a form submit from the html page
    // and will do the newOfficer function upon receiving one.
    form.addEventListener('submit', newOfficer)
}