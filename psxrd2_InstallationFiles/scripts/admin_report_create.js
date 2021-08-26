// javascript for creating new repots in the database.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(offender, vehicle, offence, date, details, author, onSuccess, onError) {
        const InputData = {
            offender: offender,
            vehicle: vehicle,
            offence: offence,
            date: date,
            details: details,
            author: author
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/submit_report.php', true)
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

                    onSuccess()

                    // If request is unsuccessfully
                } else {

                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onError(response.error)
                }
            }
        }
    }

    // Function which fills at all of the selector elements with options received from the
    // mysql database via php scripts.
    function handleSuccess(search1,search2,search3) {
        const selection1 = document.getElementById('people')
        selection1.innerHTML = search1
        const selection2 = document.getElementById('cars')
        selection2.innerHTML = search2
        const selection3 = document.getElementById('offence')
        selection3.innerHTML = search3
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess2() {
        window.location.href = 'admin_report_created.html';
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an function which will be activated once a form submission is received
    function handleLogin(event) {
        event.preventDefault()

        const offender = form.querySelector('[name=person]').value
        const vehicle = form.querySelector('[name=car]').value
        const offences = form.querySelector('[name=offences]').value
        const date = form.querySelector('[name=repdate]').value
        const details = form.querySelector('[name=report]').value
        const author = sessionStorage.getItem('officer');


        login(offender, vehicle, offences, date, details, author,handleSuccess2, handleError)
    }

    // Getting the user name for authentication purposes.
    const name = sessionStorage.getItem('officer');
    const data = {
        name: name
    }

    // Forming XMLH request in order to get the values for the selector elements
    // used in the creation of the report.
    const request1 = new XMLHttpRequest()
    request1.open('post', '../php/officer_create_report.php', true)
    request1.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request1.send(JSON.stringify(data))

    // Defining if statements if a specific response is received.
    request1.onreadystatechange = function () {
        if (request1.readyState === 4) {

            // If request is successfully
            if (request1.status === 200) {
                const responseJson = request1.responseText
                const response1 = JSON.parse(responseJson)

                handleSuccess(response1.search, response1.search1, response1.search2)

                // If request is unsuccessfully
            } else {

                const responseJson = request1.responseText
                const response = JSON.parse(responseJson)

                handleError(response.error)
            }
        }
    }

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', handleLogin)
}