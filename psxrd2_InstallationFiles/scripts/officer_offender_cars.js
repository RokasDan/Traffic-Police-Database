// javascript for viewing owned vehicles of a specific offender.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Retrieving values from a link parameter to be used in an mysql query to find the cars.
    const params = new URLSearchParams(document.location.search.substring(1));
    const owner = params.get("owner");
    const name = sessionStorage.getItem('officer');

    // Making json data to send the values to php script that will use the value in a query.
    const Data = {
        owner: owner,
        name: name
    }

    // Defining an action function which will do something on a successfully response.
    function handleSuccess(search) {
        const officerName = document.getElementById('test')
        officerName.innerHTML = search
    }

    // Defining an action function which will do something on a unsuccessfully response.
    function handleError(search) {
        const officerName = document.getElementById('test')
    }

    // Forming XMLH request.
    const request = new XMLHttpRequest()
    request.open('post', '../php/officer_offender_cars.php', true)
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
    request.send(JSON.stringify(Data))

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

                const responseJson = request.responseText
                const response = JSON.parse(responseJson)
                console.log(response.search)
                handleError(response.search)
            }
        }
    }
}