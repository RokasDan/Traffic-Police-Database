// javascript for the home page to display the currently lodged in user.

// code actives once the page is full loaded where the code is referenced.
window.onload = function () {

    // getting the user name from the sessionStorage
   const name = sessionStorage.getItem('officer');

   // If name is not empty the user name will be displayed in the element with the test id.
   if (name !== ''){

       const officerName = document.getElementById('test')
       officerName.innerText = "Officer " + name

       // if name is empty the user will be redirected to a page with an error page.
   } else {
       window.location.href = 'error.html';
   }
}