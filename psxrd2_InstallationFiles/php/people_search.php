<?php
// php script used find a all details of a specific offender.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Search = $data['search'];

// Adding values for the database connection
require "server_connection.php";

// If search is empty send a 400 response
// to the javascript file with an error message.
if (empty($Search)) {
    $data = array(
        'error' => 'Search can not be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // establishing connection to the database
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {

        // if connection fails send a 400 response
        // with an error message to the javascript file.
        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Preparing the mysql query to find all details about a specific offend who's id is like the search key.
        $sql = "SELECT people_id, first_name, last_name, date_of_birth, license_number, IFNULL(address, 'N/A') AS address FROM People WHERE people_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Search);
        $stmt->execute();
        $result = $stmt->get_result();

        // Preparing the mysql query to find number of vehicles this offender has.
        $sql1 = "SELECT * FROM cars WHERE owner = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s", $Search);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $number_of_cars = mysqli_num_rows($result1);

        // If no results received send 400 response with an error message.
        if (mysqli_num_rows($result) == 0) {

            $data = array(
                'error' => 'No results found!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // The query result values are put in to a html code message.
            // Send a 200 response to the javascript file with the html message.
            $searchResults = "";
            $string = "VEHICLE COUNT - ";
            $searchResults1 = $string . $number_of_cars;
            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<nobr><div class='container18'><h1 class='header4'>|---PERSON ID---|</h1></div></nobr>
                                   <nobr><div class='container19'><h2 class='header4'>" . $row['people_id'] . "</h2></div></nobr>
                                   <nobr><div class='container20'><h3 class='header4'>|---FIRST NAME---|</h3></div></nobr>
                                   <nobr><div class='container21'><h4 class='header4'>" . $row['first_name'] . "</h4></div></nobr>
                                   <nobr><div class='container22'><h5 class='header4'>|---LAST NAME---|</h5></div></nobr>
                                   <nobr><div class='container23'><h6 class='header4'>" . $row['last_name'] . "</h6></div></nobr>
                                   <nobr><div class='container28'><h7 class='header4'>|---BIRTH DATE---|</h7></div></nobr>
                                   <nobr><div class='container29'><h8 class='header4'>" . $row['date_of_birth'] . "</h8></div></nobr>
                                   <nobr><div class='container24'><h9 class='header4'>|LICENCE NUMBER|</h9></div></nobr>
                                   <nobr><div class='container25'><h10 class='header4'>|ADDRESS|</h10></div></nobr>
                                   <div class='container26'><p11 class='header9'>" . $row['license_number'] . "</p11></div>
                                   <div class='container27'><p12 class='header9'>" . $row['address'] . "</p12></div>";

            }

            $data = array(
                'search' => $searchResults,
                'search1'=> $searchResults1
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}