<?php
// php script used find a all vehicles according to the inputted search key.

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
        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Preparing the mysql query to find all vehicles which are like the search key.
        $sql = "SELECT cars.number_plate, cars.brand, cars.colour, people.first_name 
                FROM cars, people 
                WHERE cars.number_plate LIKE ?
                AND cars.owner = people.people_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Search1);
        $stmt->execute();
        $result = $stmt->get_result();

        // If no results received we prepare new mysql query.
        // Preparing the mysql query to find all vehicles which dont have an owner.
        if (mysqli_num_rows($result) == 0) {

            $sql1 = "SELECT cars.number_plate, cars.brand, cars.colour, Unknown.first_name 
                FROM cars, Unknown 
                WHERE cars.number_plate LIKE ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("s", $Search1);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            // If no results received send 400 response with an error message.
            if (mysqli_num_rows($result1) == 0) {

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
                $number = mysqli_num_rows($result1);
                $searchResults = "";

                while ($row = mysqli_fetch_assoc($result1)) {

                    $searchResults .= "<div class='rect19'>
                                        <div class='container8'><h9 class='header13'>" . $row['number_plate'] . "</h9></div>
                                        <div class='container9'><h6 class='header13'>" . $row['brand'] . "</h6></div>
                                        <div class='container10'><h7 class='header13'>" . $row['colour'] . "</h7></div>
                                        <div class='container11'><h8 class='header13'>" . $row['first_name'] . "</h8></div>
                                        <div class='container12'><a class='button50' href='Avehicle_results.html?cars=" . $row['number_plate'] . "'>VIEW</a></div>
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
                                        <div class='container12'><a class='button50' href='Avehicle_results.html?cars=" . $row['number_plate'] . "'>VIEW</a></div>
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

