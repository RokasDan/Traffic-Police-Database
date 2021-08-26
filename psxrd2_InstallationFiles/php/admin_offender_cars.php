<?php
// php script used find a all vehicles that belong to a specific owner.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$owner = $data['owner'];
$officer = $data['name'];

// Adding values for the database connection
require "server_connection.php";

// If officer is empty send a 400 response
// to the javascript file with an error message.
if (empty($officer)) {

    $data = array(
        'error' => 'Officer can not be empty!'
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

        // Preparing the mysql query to find details about all offender vehicles.
        $sql = "SELECT cars.number_plate, cars.brand, cars.colour, people.first_name 
                FROM cars, people 
                WHERE cars.owner = ?
                AND cars.owner = people.people_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $owner);
        $stmt->execute();
        $result = $stmt->get_result();

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
            $number = mysqli_num_rows($result);
            $searchResults = "";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<div class='rect19'>
                                        <div class='container8'><h9 class='header13'>" . $row['number_plate'] . "</h9></div>
                                        <div class='container9'><h6 class='header13'>" . $row['brand'] . "</h6></div>
                                        <div class='container10'><h7 class='header13'>" . $row['colour'] . "</h7></div>
                                        <div class='container11'><h8 class='header13'>" . $row['first_name'] . "</h8></div>
                                        <div class='container12'><a class='button50' href='officer_offender_vehicle_details.html?cars=" . $row['number_plate'] . "'>VIEW</a></div>
                                   </div>";
            }

            $data = array(
                'error' => $number,
                'search' => $searchResults
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}