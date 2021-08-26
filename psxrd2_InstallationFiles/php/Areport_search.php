<?php
// php script used find a all details of a specific report.

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

        // Preparing the mysql query to find all details about a specific report who's id or other attributes are like the search key.
        $sql = "SELECT reports.report_id, reports.author, reports.car_id, reports.people_id, reports.offence_id, 
                       IFNULL(reports.fine_issued, 'N/A') AS fine_issued, IFNULL(reports.points_issued, 'N/A') AS points_issued, reports.report_date, reports.details, people.first_name, 
                       people.last_name, people.date_of_birth, IFNULL(people.license_number, 'N/A') AS license_number, IFNULL(people.address, 'N/A') AS address, cars.brand, IFNULL(cars.model, 'N/A') AS model,
                       cars.colour, offence.description
                FROM reports, offence, cars, people
                WHERE report_id = ?
                AND reports.car_id = cars.number_plate 
                AND reports.people_id = people.people_id 
                AND reports.offence_id = offence.Offence_ID";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Search);
        $stmt->execute();
        $result = $stmt->get_result();

        // If no results received we prepare a new mysql query which searches for reports that do not
        // do not have a car_id.
        if (mysqli_num_rows($result) == 0) {

            $sql1 = "SELECT reports.report_id, reports.author, IFNULL(reports.car_id, 'N/A') AS car_id, reports.people_id, reports.offence_id, 
                       IFNULL(reports.fine_issued, 'N/A') AS fine_issued, IFNULL(reports.points_issued, 'N/A') AS points_issued, reports.report_date, reports.details, people.first_name, 
                       people.last_name, people.date_of_birth, IFNULL(people.license_number, 'N/A') AS license_number, IFNULL(people.address, 'N/A') AS address, Unknown.first_name AS brand, Unknown.first_name AS model,
                       Unknown.first_name AS colour, offence.description
                FROM reports, offence, Unknown, people
                WHERE report_id = ?
                AND reports.people_id = people.people_id 
                AND reports.offence_id = offence.Offence_ID";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("s", $Search);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            // If no results received we prepare a new mysql query which searches for reports that do not
            // do not have an offender_id.
            if (mysqli_num_rows($result1) == 0) {

                $sql2 = "SELECT reports.report_id, reports.author, reports.car_id, IFNULL(people_id, 'N/A') AS people_id, reports.offence_id, 
                       IFNULL(reports.fine_issued, 'N/A') AS fine_issued, IFNULL(reports.points_issued, 'N/A') AS points_issued, reports.report_date, reports.details, Unknown.first_name AS first_name, 
                       Unknown.first_name AS last_name, Unknown.first_name AS date_of_birth, Unknown.first_name AS license_number, Unknown.first_name address, cars.brand, IFNULL(cars.model, 'N/A') AS model,
                       cars.colour, offence.description
                FROM reports, offence, cars, Unknown
                WHERE report_id = ?
                AND reports.car_id = cars.number_plate  
                AND reports.offence_id = offence.Offence_ID";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("s", $Search);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                // if no results are found this time a response of 400 is sent with an error message.
                if (mysqli_num_rows($result2) == 0) {

                    $data = array(
                        'search' => "Error in the search"
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);
                    die();

                } else {

                    // The query result values are put in to a html code message.
                    // Send a 200 response to the javascript file with the html message.
                    $searchResults = "";

                    while ($row = mysqli_fetch_assoc($result2)) {

                        $searchResults .= "<nobr><div class='container49'><h1 class='header4'>THE INCIDENT</h1></div></nobr>
                                   <nobr><div class='container51'><h1 class='header4'>THE OFFENDER</h1></div></nobr>
                                   <nobr><div class='container52'><h1 class='header4'>THE VEHICLE</h1></div></nobr>
                                <div class='container53'>
                                    <div class='container56'><nobr><h1 class='header4'>|REPORT ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['report_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|AUTHOR|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['author'] . "</h1></nobr></div>
                                    <div class='container60'><a class='button28' href='admin_update_rdate.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|DATE|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['report_date'] . "</h1></nobr></div>
                                    <div class='container62'><a class='button28' href='admin_update_fine.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|FINE|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['fine_issued'] . "</h1></nobr></div>
                                    <div class='container64'><a class='button28' href='admin_update_points.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|POINTS|</h1></nobr></div>
                                    <div class='container68'><nobr><h1 class='header4'>" . $row['points_issued'] . "</h1></nobr></div>
                                    <div class='container69'><a class='button28' href='admin_update_offence.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|OFFENCE_ID|</h1></nobr></div>
                                    <div class='container70'><nobr><h1 class='header4'>" . $row['offence_id'] . "</h1></nobr></div>
                                    <div class='container71'><nobr><h1 class='header4'>|DESCRIPTION|</h1></nobr></div>
                                    <div class='container72'><p1 class='header10'>" . $row['description'] . "</p1></nobr></div>
                                    <div class='container73'><a class='button28' href='admin_update_report.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|REPORT|</h1></nobr></div>
                                    <div class='container74'><p2 class='header10'>" . $row['details'] . "</p2></nobr></div>
                                </div>
                                <div class='container54'>
                                    <div class='container56'><nobr><h1 class='header4'>|PERSON ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['people_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|FIST NAME|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['first_name'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|LAST NAME|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['last_name'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|DOB|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['date_of_birth'] . "</h1></nobr></div>
                                    <div class='container64'><nobr><h1 class='header4'>|LICENSE|</h1></nobr></div>
                                    <div class='container65'><nobr><h1 class='header4'>" . $row['license_number'] . "</h1></nobr></div>
                                    <div class='container66'><nobr><h1 class='header4'>|ADDRESS|</h1></nobr></div>
                                    <div class='container67'><p1 class='header10'>" . $row['address'] . "</p1></nobr></div>
                                </div>
                                <div class='container55'>
                                    <div class='container56'><nobr><h1 class='header4'>|VEHICLE_ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['car_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|BRAND|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['brand'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|MODEL|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['model'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|COLOUR|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['colour'] . "</h1></nobr></div>
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


            } else {

                // The query result values are put in to a html code message.
                // Send a 200 response to the javascript file with the html message.
                $searchResults = "";

                while ($row = mysqli_fetch_assoc($result1)) {

                    $searchResults .= "<nobr><div class='container49'><h1 class='header4'>THE INCIDENT</h1></div></nobr>
                                   <nobr><div class='container51'><h1 class='header4'>THE OFFENDER</h1></div></nobr>
                                   <nobr><div class='container52'><h1 class='header4'>THE VEHICLE</h1></div></nobr>
                                <div class='container53'>
                                    <div class='container56'><nobr><h1 class='header4'>|REPORT ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['report_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|AUTHOR|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['author'] . "</h1></nobr></div>
                                    <div class='container60'><a class='button28' href='admin_update_rdate.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|DATE|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['report_date'] . "</h1></nobr></div>
                                    <div class='container62'><a class='button28' href='admin_update_fine.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|FINE|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['fine_issued'] . "</h1></nobr></div>
                                    <div class='container64'><a class='button28' href='admin_update_points.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|POINTS|</h1></nobr></div>
                                    <div class='container68'><nobr><h1 class='header4'>" . $row['points_issued'] . "</h1></nobr></div>
                                    <div class='container69'><a class='button28' href='admin_update_offence.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|OFFENCE_ID|</h1></nobr></div>
                                    <div class='container70'><nobr><h1 class='header4'>" . $row['offence_id'] . "</h1></nobr></div>
                                    <div class='container71'><nobr><h1 class='header4'>|DESCRIPTION|</h1></nobr></div>
                                    <div class='container72'><p1 class='header10'>" . $row['description'] . "</p1></nobr></div>
                                    <div class='container73'><a class='button28' href='admin_update_report.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|REPORT|</h1></nobr></div>
                                    <div class='container74'><p2 class='header10'>" . $row['details'] . "</p2></nobr></div>
                                </div>
                                <div class='container54'>
                                    <div class='container56'><nobr><h1 class='header4'>|PERSON ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['people_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|FIST NAME|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['first_name'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|LAST NAME|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['last_name'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|DOB|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['date_of_birth'] . "</h1></nobr></div>
                                    <div class='container64'><nobr><h1 class='header4'>|LICENSE|</h1></nobr></div>
                                    <div class='container65'><nobr><h1 class='header4'>" . $row['license_number'] . "</h1></nobr></div>
                                    <div class='container66'><nobr><h1 class='header4'>|ADDRESS|</h1></nobr></div>
                                    <div class='container67'><p1 class='header10'>" . $row['address'] . "</p1></nobr></div>
                                </div>
                                <div class='container55'>
                                    <div class='container56'><nobr><h1 class='header4'>|VEHICLE_ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['car_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|BRAND|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['brand'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|MODEL|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['model'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|COLOUR|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['colour'] . "</h1></nobr></div>
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

        } else {

            // The query result values are put in to a html code message.
            // Send a 200 response to the javascript file with the html message.
            $searchResults = "";

            while ($row = mysqli_fetch_assoc($result)) {

                $searchResults .= "<nobr><div class='container49'><h1 class='header4'>THE INCIDENT</h1></div></nobr>
                                   <nobr><div class='container51'><h1 class='header4'>THE OFFENDER</h1></div></nobr>
                                   <nobr><div class='container52'><h1 class='header4'>THE VEHICLE</h1></div></nobr>
                                <div class='container53'>
                                    <div class='container56'><nobr><h1 class='header4'>|REPORT ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['report_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|AUTHOR|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['author'] . "</h1></nobr></div>
                                    <div class='container60'><a class='button28' href='admin_update_rdate.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|DATE|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['report_date'] . "</h1></nobr></div>
                                    <div class='container62'><a class='button28' href='admin_update_fine.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|FINE|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['fine_issued'] . "</h1></nobr></div>
                                    <div class='container64'><a class='button28' href='admin_update_points.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|POINTS|</h1></nobr></div>
                                    <div class='container68'><nobr><h1 class='header4'>" . $row['points_issued'] . "</h1></nobr></div>
                                    <div class='container69'><a class='button28' href='admin_update_offence.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|OFFENCE_ID|</h1></nobr></div>
                                    <div class='container70'><nobr><h1 class='header4'>" . $row['offence_id'] . "</h1></nobr></div>
                                    <div class='container71'><nobr><h1 class='header4'>|DESCRIPTION|</h1></nobr></div>
                                    <div class='container72'><p1 class='header10'>" . $row['description'] . "</p1></nobr></div>
                                    <div class='container73'><a class='button28' href='admin_update_report.html?update=" . $row['report_id'] . "'>#</a><nobr><h1 class='header4'>|REPORT|</h1></nobr></div>
                                    <div class='container74'><p2 class='header10'>" . $row['details'] . "</p2></nobr></div>
                                </div>
                                <div class='container54'>
                                    <div class='container56'><nobr><h1 class='header4'>|PERSON ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['people_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|FIST NAME|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['first_name'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|LAST NAME|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['last_name'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|DOB|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['date_of_birth'] . "</h1></nobr></div>
                                    <div class='container64'><nobr><h1 class='header4'>|LICENSE|</h1></nobr></div>
                                    <div class='container65'><nobr><h1 class='header4'>" . $row['license_number'] . "</h1></nobr></div>
                                    <div class='container66'><nobr><h1 class='header4'>|ADDRESS|</h1></nobr></div>
                                    <div class='container67'><p1 class='header10'>" . $row['address'] . "</p1></nobr></div>
                                </div>
                                <div class='container55'>
                                    <div class='container56'><nobr><h1 class='header4'>|VEHICLE_ID|</h1></nobr></div>
                                    <div class='container57'><nobr><h1 class='header4'>" . $row['car_id'] . "</h1></nobr></div>
                                    <div class='container58'><nobr><h1 class='header4'>|BRAND|</h1></nobr></div>
                                    <div class='container59'><nobr><h1 class='header4'>" . $row['brand'] . "</h1></nobr></div>
                                    <div class='container60'><nobr><h1 class='header4'>|MODEL|</h1></nobr></div>
                                    <div class='container61'><nobr><h1 class='header4'>" . $row['model'] . "</h1></nobr></div>
                                    <div class='container62'><nobr><h1 class='header4'>|COLOUR|</h1></nobr></div>
                                    <div class='container63'><nobr><h1 class='header4'>" . $row['colour'] . "</h1></nobr></div>
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