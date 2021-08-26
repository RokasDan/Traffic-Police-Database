// javascript for updating the offence type in the report.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(update, value, onSuccess, onError) {
        const InputData = {
            value: value,
            update: update
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/update_offence.php', true)
        request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
        request.send(JSON.stringify(InputData))

        // Defining if statements if a specific response is received.
        request.onreadystatechange = function () {
            if (request.readyState === 4) {

                // If request is successfully
                if (request.status === 200) {
                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    console.log(response.error)

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

    // function which fill out the selector element with in the page
    // with options received from mysql server via php script.
    function handleSuccess(search) {
        const selection1 = document.getElementById('offence')
        selection1.innerHTML = search
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess2(message) {
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

    // Defining an function which will be activated once a form submission is received.
    function handleLogin(event) {
        event.preventDefault()

        const value = form.querySelector('[name=value]').value
        const params = new URLSearchParams(document.location.search.substring(1));
        const update = params.get("update");


        login(update, value,handleSuccess2, handleError)
    }

    // Making json data for authentication purposes.
    const name = sessionStorage.getItem('officer');
    const data = {
        name: name
    }

    // Forming XMLH request which will request data from mysql
    // to fill the selector element with options from the database.
    const request1 = new XMLHttpRequest()
    request1.open('post', '../php/officer_update_offence.php', true)
    request1.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request1.send(JSON.stringify(data))

    // Defining if statements if a specific response is received.
    request1.onreadystatechange = function () {
        if (request1.readyState === 4) {

            // If request is successfully
            if (request1.status === 200) {
                const responseJson = request1.responseText
                const response1 = JSON.parse(responseJson)

                handleSuccess(response1.search)

                // If request is unsuccessfully
            } else {

                const responseJson = request1.responseText
                const response1 = JSON.parse(responseJson)

                handleError(response1.error)

            }
        }
    }

    // getting the values from the link parameters in order to create
    // a back link to go back to an updated report.
    const params1 = new URLSearchParams(document.location.search.substring(1));
    const goback = params1.get("update");
    const backbutton = document.getElementById('back')
    const link = "report_results.html?reports=" + goback
    backbutton.setAttribute('href', link);

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', handleLogin)
}