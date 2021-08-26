<?php

// php script used to fetch details about a specific offence type.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Search = $data['search'];

// Adding values for the database connection
require "server_connection.php";

// Checking if the value for the search is empty
// if so php send a response of 400 with an error message.
if (empty($Search)) {
    $data = array(
        'error' => 'Search can not be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // establishing a connection with mysql server.

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {

        // if connection fails send a 400 response to the
        //javascript file and an error message.

        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Preparing mysql query with the search key.

        $sql = "SELECT * FROM Offence WHERE Offence_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Search);
        $stmt->execute();
        $result = $stmt->get_result();

        // If query gets 0 results php send 400 response to
        // to the javascript file with an error message.
        if (mysqli_num_rows($result) == 0) {

            $data = array(
                'error' => 'No results found!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // extracting the values received from the query and forming a message
            // containing html code with the values received from the query. Sending
            // the message with a 200 response to a javascript file.

            $searchResults = "";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<div>
                                        <nobr><div class='container34'><h1 class='header4'>|---OFFENCE ID---|</h1></div></nobr>
                                        <nobr><div class='container35'><h2 class='header4'>" . $row['Offence_ID'] . "</h2></div></nobr>
                                        <nobr><div class='container36'><h3 class='header4'>|----MAX FINE----|</h3></div></nobr>
                                        <nobr><div class='container37'><h4 class='header4'>" . $row['maxFine'] . "</h4></div></nobr>
                                        <nobr><div class='container38'><h5 class='header4'>|---MAX POINTS---|</h5></div></nobr>
                                        <nobr><div class='container39'><h6 class='header4'>" . $row['maxPoints'] . "</h6></div></nobr>
                                        <nobr><div class='container40'><h7 class='header4'>|---------DESCRIPTION---------|</h7></div></nobr>
                                        <nobr><div class='container41'><p1 class='header10'>" . $row['description'] . "</p1></div></nobr>
                                   </div>";

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
