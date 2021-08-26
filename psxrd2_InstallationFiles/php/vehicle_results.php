<?php
// php script used to show details of a specific vehicle.

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

        //Preparing mysql query to find all details of a specific car.number_plates that,s like the search key.
        $sql = "SELECT cars.number_plate, cars.brand, IFNULL(cars.model, 'N/A') AS model, cars.colour, IFNULL(cars.owner, 'N/A') AS owner, people.first_name, people.last_name
        FROM cars, people
        WHERE cars.number_plate = ?
        AND cars.owner = people.people_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Search);
        $stmt->execute();
        $result = $stmt->get_result();

        // If no results received we prepare a new mysql query but this time we search for cars that dont have owners.
        if (mysqli_num_rows($result) == 0) {

            $sql1 = "SELECT cars.number_plate, cars.brand, IFNULL(cars.model, 'N/A') AS model, cars.colour, IFNULL(cars.owner, 'N/A') AS owner, Unknown.first_name, Unknown.last_name
            FROM cars, Unknown
            WHERE cars.number_plate = ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("s", $Search);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            // if 0 results found now we send a 400 response with an error message.
           if (mysqli_num_rows($result1) == 0){

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

               while ($row = mysqli_fetch_assoc($result1)) {

                   $searchResults .= "<nobr><div class='container18'><h1 class='header4'>|--NUMBER PLATE--|</h1></div></nobr>
                                   <nobr><div class='container19'><h2 class='header4'>" . $row['number_plate'] . "</h2></div></nobr>
                                   <nobr><div class='container20'><h3 class='header4'>|---CAR BRAND---|</h3></div></nobr>
                                   <nobr><div class='container21'><h4 class='header4'>" . $row['brand'] . "</h4></div></nobr>
                                   <nobr><div class='container22'><h5 class='header4'>|---CAR MODEL---|</h5></div></nobr>
                                   <nobr><div class='container23'><h6 class='header4'>" . $row['model'] . "</h6></div></nobr>
                                   <nobr><div class='container28'><h7 class='header4'>|---CAR COLOUR---|</h7></div></nobr>
                                   <nobr><div class='container29'><h8 class='header4'>" . $row['colour'] . "</h8></div></nobr>
                                   <nobr><div class='container24'><h9 class='header4'>|OWNER NAME|</h9></div></nobr>
                                   <nobr><div class='container25'><h10 class='header4'>|OWNER SURNAME|</h10></div></nobr>
                                   <div class='container26'><p11 class='header9'>" . $row['first_name'] . "</p11></div>
                                   <div class='container27'><p12 class='header9'>" . $row['last_name'] . "</p12></div>
                                   <nobr><div class='container31'><h10 class='header4'>|OWNER ID|</h10></div></nobr>
                                   <div class='container32'><p12 class='header4'>" . $row['owner'] . "</p12></div>";

               }


               $data = array(
                   'search' => $searchResults
               );

               header('HTTP/1.0 200');
               header('Content-Type: application/json');

               echo json_encode($data);
               die();

           }

        } else {

            // The query result values are put in to a html code message.
            // Send a 200 response to the javascript file with the html message.
            $searchResults = "";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<nobr><div class='container18'><h1 class='header4'>|--NUMBER PLATE--|</h1></div></nobr>
                                   <nobr><div class='container19'><h2 class='header4'>" . $row['number_plate'] . "</h2></div></nobr>
                                   <nobr><div class='container20'><h3 class='header4'>|---CAR BRAND---|</h3></div></nobr>
                                   <nobr><div class='container21'><h4 class='header4'>" . $row['brand'] . "</h4></div></nobr>
                                   <nobr><div class='container22'><h5 class='header4'>|---CAR MODEL---|</h5></div></nobr>
                                   <nobr><div class='container23'><h6 class='header4'>" . $row['model'] . "</h6></div></nobr>
                                   <nobr><div class='container28'><h7 class='header4'>|---CAR COLOUR---|</h7></div></nobr>
                                   <nobr><div class='container29'><h8 class='header4'>" . $row['colour'] . "</h8></div></nobr>
                                   <nobr><div class='container24'><h9 class='header4'>|OWNER NAME|</h9></div></nobr>
                                   <nobr><div class='container25'><h10 class='header4'>|OWNER SURNAME|</h10></div></nobr>
                                   <div class='container26'><p11 class='header9'>" . $row['first_name'] . "</p11></div>
                                   <div class='container27'><p12 class='header9'>" . $row['last_name'] . "</p12></div>
                                   <nobr><div class='container31'><h10 class='header4'>|OWNER ID|</h10></div></nobr>
                                   <div class='container32'><p12 class='header4'>" . $row['owner'] . "</p12></div>";

            }


            $data = array(
                'search' => $searchResults
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}
