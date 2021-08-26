// javascript for updating the POINTS in the report.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(update, value, onSuccess, onError) {
        const loginData = {
            update: update,
            value: value
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/admin_point_update.php', true)
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
    function handleSuccess(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleLogin(event) {
        event.preventDefault()

        const params = new URLSearchParams(document.location.search.substring(1));
        const update = params.get("update");
        const value = form.querySelector('[name=value]').value

        login(update, value,handleSuccess, handleError)
    }

    // getting the values from the link parameters in order to create
    // a back link to go back to an updated report.
    const params1 = new URLSearchParams(document.location.search.substring(1));
    const goback = params1.get("update");
    const backbutton = document.getElementById('back')
    const link = "Areport_results.html?reports=" + goback
    backbutton.setAttribute('href', link);

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', handleLogin)
}