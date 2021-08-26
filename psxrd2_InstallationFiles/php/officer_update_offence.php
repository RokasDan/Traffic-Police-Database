<?php
// php script used to get all possible offence types for a report update.

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

        // Preparing the mysql query to find all people offence types.
        $sql = "SELECT Offence_ID, description, maxFine FROM offence";
        $stmt = $conn->prepare($sql);
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

            // The query result values are put in to a html code message to update the selector element.
            // Send a 200 response to the javascript file with the html message.
            $number = mysqli_num_rows($result);
            $searchResults1 = "<option value=''>EMPTY|SELECT THE OFFENCE TYPE</option>";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults1 .= "<option value='" . $row['Offence_ID'] . "'>" . $row['Offence_ID'] . " " . $row['description'] . " (£" . $row['maxFine'] . ")</option>";

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
