<?php
// php script used find a all people according to the inputted search key.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Search = $data['search'];
$Search1 = "%" . $Search . "%";

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

        // Preparing the mysql query to find all people which are like the search key.
        $sql = "SELECT people_id, first_name, last_name, IFNULL(license_number, 'N/A') AS license_number FROM people
                WHERE first_name LIKE ? OR last_name LIKE ? OR license_number LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $Search1, $Search1, $Search1);
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

                $searchResults .= "<div class='rect9'>
                                        <div class='container8'><h9 class='header3'>" . $row['people_id'] . "</h9></div>
                                        <div class='container9'><h6 class='header3'>" . $row['first_name'] . "</h6></div>
                                        <div class='container10'><h7 class='header3'>" . $row['last_name'] . "</h7></div>
                                        <div class='container11'><h8 class='header3'>" . $row['license_number'] . "</h8></div>
                                        <div class='container12'><a class='button13' href='people_results.html?people=" . $row['people_id'] . "'>VIEW</a></div>
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
