<?php

// php script which prepares all of the selector elements
// to be used by the user in order to create a new report.

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

        // if connection fails send a 400 response with an error message
        // to the javascript file.
        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Create mysql queries to select all options for offender
        // vehicle and offence type selector elements.

        $sql = "SELECT people_id, first_name, last_name, date_of_birth FROM people";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $sql1 = "SELECT number_plate, brand, model FROM cars";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        $sql2 = "SELECT Offence_ID, description, maxFine FROM offence";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        //if even one query does not return any results send a 400 response
        // to the javascript file with an error message.
        if (mysqli_num_rows($result) == 0 or mysqli_num_rows($result1) == 0 or mysqli_num_rows($result2) == 0) {

            $data = array(
                'error' => 'No results found!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            $number = mysqli_num_rows($result);

            //prepare empty options
            $searchResults1 = "<option value=''>EMPTY|SELECT AN OFFENDER</option>";
            $searchResults2 = "<option value=''>EMPTY|SELECT A VEHICLE</option>";
            $searchResults3 = "<option value=''>EMPTY|SELECT THE OFFENCE TYPE</option>";


            // Prepare the options with values and store them as html code to messages
            // to be sent to the javascript file.
            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults1 .= "<option value='" . $row['people_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . " (" . $row['date_of_birth'] . ")</option>";

            }

            while ($row = mysqli_fetch_assoc($result1)) {

                $searchResults2 .= "<option value='" . $row['number_plate'] . "'>" . $row['brand'] . " " . $row['model'] . " (" . $row['number_plate'] . ")</option>";

            }

            while ($row = mysqli_fetch_assoc($result2)) {

                $searchResults3 .= "<option value='" . $row['Offence_ID'] . "'>" . $row['Offence_ID'] . " " . $row['description'] . " (£" . $row['maxFine'] . ")</option>";

            }

            // send a 200 response and the messages with the html
            // code to the javascript file.

            $data = array(
                'error' => $number,
                'search' => $searchResults1,
                'search1'=> $searchResults2,
                'search2'=> $searchResults3
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}
