// javascript for creating adding a new vehicle to the database.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // selector which selects the form element with in the html page.
    const form = document.querySelector('form')

    // A function which create an XMLH request to a php script
    // The function will active handleSuccess() function if 200 response is received
    // The function will active handleError() function if 400 response is received
    function login(plate, brand, model, colour, owner, onSuccess, onError) {
        const InputData = {
            plate: plate,
            brand: brand,
            model: model,
            colour: colour,
            owner: owner,
        }

        // Forming XMLH request.
        const request = new XMLHttpRequest()
        request.open('post', '../php/officer_add_vehicle.php', true)
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

                    // If request is unsuccessfully.
                } else {

                    const responseJson = request.responseText
                    const response = JSON.parse(responseJson)

                    onError(response.error)
                }
            }
        }
    }

    // Defining an action function which will add options to the selection tag
    // after receiving information from mysql database.
    function handleSuccess(search) {
        const selection1 = document.getElementById('people')
        selection1.innerHTML = search
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess2() {
        window.location.href = 'admin_vehicle_added.html';
    }

    // Defining an action function which will do something on an unsuccessfully response.
    function handleError(message) {
        const errordiv = document.getElementById('error-div')
        errordiv.style.display = 'block'

        const errorMessage = document.getElementById('error-message')
        errorMessage.innerText = message
    }

    // Defining an function which will be activated once a form submission is received.
    function addVehicle(event) {
        event.preventDefault()

        const plate = form.querySelector('[name=plate]').value
        const brand = form.querySelector('[name=brand]').value
        const model = form.querySelector('[name=model]').value
        const colour = form.querySelector('[name=colour]').value
        const owner = form.querySelector('[name=person]').value


        login(plate, brand, model, colour, owner,handleSuccess2, handleError)
    }

    // Creating json data will be used as authentication.
    const name = sessionStorage.getItem('officer');
    const data = {
        name: name
    }

    // Forming XMLH request for filling up the selector element with options in the page.
    const request1 = new XMLHttpRequest()
    request1.open('post', '../php/officer_add_owner.php', true)
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
                const response = JSON.parse(responseJson)

                handleError(response.error)

            }
        }
    }

    // Making an event listener which will wait for a form submit from the html page
    // and will do the handleLogin function upon receiving one.
    form.addEventListener('submit', addVehicle)
}