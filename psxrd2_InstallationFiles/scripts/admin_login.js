// javascript for admin login to the website.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(username, password, onSuccess, onError) {

        // creating json data for the XMLH request.
        const loginData = {
            username: username,
            password: password
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/admin_login.php', true)
        request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
        request.send(JSON.stringify(loginData))

        // Defining if statements if a specific response is received.
        request.onreadystatechange = function () {
            if (request.readyState === 4) {

                // If request is successfully
                if (request.status === 200) {
                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onSuccess(response.name)

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
    function handleSuccess(name) {
        sessionStorage.setItem("officer", name);
        window.location.href = 'admin_home.html';
    }

    // Defining an action function which will do something on an unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an function which will be activated once a form submission is received.
    function handleLogin(event) {
        event.preventDefault()

        const username = form.querySelector('[name=username]').value
        const password = form.querySelector('[name=password]').value

        login(username, password, handleSuccess, handleError)
    }

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', handleLogin)
}