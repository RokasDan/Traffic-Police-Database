<?php
// php script used find a all reports according to the inputted search key.

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

        // Preparing the mysql query to find all reports which are like the search key.

        $sql = "SELECT reports.report_id, people.first_name, people.license_number, cars.number_plate, reports.report_date 
                FROM reports, people, cars
                WHERE reports.report_id = ? AND people.people_id = reports.people_id AND cars.number_plate = car_id
                OR people.first_name LIKE ? AND people.people_id = reports.people_id AND cars.number_plate = car_id
                OR people.last_name LIKE ? AND people.people_id = reports.people_id AND cars.number_plate = car_id
                OR people.license_number = ? AND people.people_id = reports.people_id AND cars.number_plate = car_id
                OR cars.number_plate = ? AND people.people_id = reports.people_id AND cars.number_plate = car_id
                OR reports.report_date LIKE ? AND people.people_id = reports.people_id AND cars.number_plate = car_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $Search, $Search1, $Search1, $Search, $Search, $Search1);
        $stmt->execute();
        $result = $stmt->get_result();

        // If results come up we prepare a new mysql query.
        // This query will look in to reports that dont have an offender ID.
        if (mysqli_num_rows($result) == 0) {

            $sql1 = "SELECT reports.report_id, unknown.first_name AS first_name, unknown.first_name AS license_number, cars.number_plate, reports.report_date 
                    FROM reports, unknown, cars
                    WHERE reports.report_id = ? AND cars.number_plate = car_id
                    OR cars.number_plate = ? AND cars.number_plate = car_id
                    OR reports.report_date LIKE ? AND cars.number_plate = car_id";

            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("sss", $Search, $Search, $Search1);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            // If results come up we prepare a new mysql query.
            // This query will look in to reports that dont have an vehicle ID.
            if (mysqli_num_rows($result1) == 0){

                $sql2 = "SELECT reports.report_id, people.first_name, IFNULL(people.license_number, 'N/A') AS license_number, unknown.first_name AS number_plate, reports.report_date 
                FROM reports, people, unknown
                WHERE reports.report_id = ? AND people.people_id = reports.people_id 
                OR people.first_name LIKE ? AND people.people_id = reports.people_id 
                OR people.last_name LIKE ? AND people.people_id = reports.people_id 
                OR people.license_number = ? AND people.people_id = reports.people_id 
                OR reports.report_date LIKE ? AND people.people_id = reports.people_id";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("sssss", $Search, $Search1, $Search1, $Search, $Search1);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                // If no results received send 400 response with an error message.
                if (mysqli_num_rows($result2) == 0) {

                    $data = array(
                        'error' => "Did not find any results!"
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);
                    die();

                } else {

                    // The query result values are put in to a html code message.
                    // Send a 200 response to the javascript file with the html message.
                    $number = mysqli_num_rows($result2);
                    $searchResults = "";

                    while ($row = mysqli_fetch_assoc($result2)) {

                        $searchResults .= "<div class='rect20'>
                                        <div class='container8'><h9 class='header13'>" . $row['report_id'] . "</h9></div>
                                        <div class='container43'><h6 class='header13'>" . $row['first_name'] . "</h6></div>
                                        <div class='container44'><h7 class='header13'>" . $row['license_number'] . "</h7></div>
                                        <div class='container45'><h8 class='header13'>" . $row['number_plate'] . "</h8></div>
                                        <nobr><div class='container46'><h8 class='header13'>" . $row['report_date'] . "</h8></div></nobr>
                                        <div class='container47'><a class='button50' href='Areport_results.html?reports=" . $row['report_id'] . "'>VIEW</a></div>
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
                $number = mysqli_num_rows($result1);
                $searchResults = "";

                while ($row = mysqli_fetch_assoc($result1)) {

                    $searchResults .= "<div class='rect20'>
                                        <div class='container8'><h9 class='header13'>" . $row['report_id'] . "</h9></div>
                                        <div class='container43'><h6 class='header13'>" . $row['first_name'] . "</h6></div>
                                        <div class='container44'><h7 class='header13'>" . $row['license_number'] . "</h7></div>
                                        <div class='container45'><h8 class='header13'>" . $row['number_plate'] . "</h8></div>
                                        <nobr><div class='container46'><h8 class='header13'>" . $row['report_date'] . "</h8></div></nobr>
                                        <div class='container47'><a class='button50' href='Areport_results.html?reports=" . $row['report_id'] . "'>VIEW</a></div>
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

                $searchResults .= "<div class='rect20'>
                                        <div class='container8'><h9 class='header13'>" . $row['report_id'] . "</h9></div>
                                        <div class='container43'><h6 class='header13'>" . $row['first_name'] . "</h6></div>
                                        <div class='container44'><h7 class='header13'>" . $row['license_number'] . "</h7></div>
                                        <div class='container45'><h8 class='header13'>" . $row['number_plate'] . "</h8></div>
                                        <nobr><div class='container46'><h8 class='header13'>" . $row['report_date'] . "</h8></div></nobr>
                                        <div class='container47'><a class='button50' href='Areport_results.html?reports=" . $row['report_id'] . "'>VIEW</a></div>
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