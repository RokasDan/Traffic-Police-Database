// javascript used display the results of a people search.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // Getting the values from session storage in order to showcase the results.
    const name = sessionStorage.getItem('officer');
    const search = sessionStorage.getItem('people');
    const resNumber = sessionStorage.getItem('numberResults');

    // if officer is valid pt the results in an element with an id of test.
    if (name !== ''){

        const officerName = document.getElementById('test')
        officerName.innerHTML = search

        // else show an error page
    } else {
        window.location.href = 'error.html';
    }
}