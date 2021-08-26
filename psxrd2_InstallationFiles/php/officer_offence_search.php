<?php

// php script which is used to fill the search results after an offence search.

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

        // Preparing the mysql query to everything that belongs
        // to this offence.
        $sql = "SELECT * FROM Offence";
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

            // The query result values are put in to a html code message.
            // Send a 200 response to the javascript file with the html message.
            $number = mysqli_num_rows($result);
            $searchResults = "";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<div class='rect11'>
                                        <div class='container13'><h9 class='header3'>" . $row['Offence_ID'] . "</h9></div>
                                        <div class='container14'><h6 class='header3'>" . $row['description'] . "</h6></div>
                                        <div class='container15'><a class='button13' href='offence_details.html?details=" . $row['Offence_ID'] . "'>VIEW</a></div>
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

