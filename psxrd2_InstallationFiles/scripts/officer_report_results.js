// javascript for showing the report results found with in report search.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting the values of the report search stored in the session storage.
    const name = sessionStorage.getItem('officer');
    const search = sessionStorage.getItem('reports');
    const resNumber = sessionStorage.getItem('numberResults');

    // If officer is not empty send the results to the element with test id
    if (name !== ''){

        const officerName = document.getElementById('test')
        officerName.innerHTML = search

        // else send the user to an error screen.
    } else {
        window.location.href = 'error.html';
    }
}