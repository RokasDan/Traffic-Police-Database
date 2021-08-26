<?php

// php script which fills up the selector options for the owner
// when adding a new vehicle to the system.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Search = $data['name'];

// Adding values for the database connection
require "server_connection.php";

// If search is empty send a 400 response
// to the javascript file with an error message.
if (empty($Search)) {

    $data = array(
        'error' => 'Officer not logged in!!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // establish a connection to the mysql server

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {

        // if connection fails send a 400 response with an error message.
        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Prepare a query to find all possible people to own a vehicle.

        $sql = "SELECT people_id, first_name, last_name, date_of_birth FROM people";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        // If query does not return anything
        // send 400 response with an error message.
        if (mysqli_num_rows($result) == 0) {

            $data = array(
                'error' => 'No results found!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // retrieve the values from the query and put them in to html option tags to form a message.
            // Send a 200 response with the html message to the javascript file.
            $number = mysqli_num_rows($result);
            $searchResults1 = "<option value=''>ANONYMOUS|SELECT AN OWNER</option>";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults1 .= "<option value='" . $row['people_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . " (" . $row['date_of_birth'] . ")</option>";

            }

            $data = array(
                'error' => $number,
                'search' => $searchResults1
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}
