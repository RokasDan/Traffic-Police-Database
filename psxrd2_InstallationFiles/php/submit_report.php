<?php
// php script used to submit a new report.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$offender = $data['offender'];
$vehicle = $data['vehicle'];
$offence = $data['offence'];
$date = $data['date'];
$details = $data['details'];
$author = $data['author'];

// Adding values for the database connection
require "server_connection.php";

// If offender or vehicle is empty send a 400 response
// to the javascript file with an error message.
if (empty($offender) and empty($vehicle)) {
    $data = array(
        'error' => 'Vehicle and offender choice cant be empty both at once!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {
    // If only offender is empty send a 400 response
    // to the javascript file with an error message.
    if (empty($offence)) {

        $data = array(
            'error' => 'Please choose the offence type!'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);

    } else {

        // If only details is empty send a 400 response
        // to the javascript file with an error message.
        if (empty($details)) {

            $data = array(
                'error' => 'Please describe the incident with no more than 150 symbols!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
        } else {

            // If only details is longer than 150 symbols send a 400 response
            // to the javascript file with an error message.
            if (strlen($details) > 150) {

                $data = array(
                    'error' => 'Your description is bigger than 150 symbols!'
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

                    // if offender is empty Preparing the mysql query to insert all details
                    // of the report but only everything about the offender and fine and points is null.
                    // create the report and send a 200 response with a succession message.
                    if (empty($offender)){

                        $sql1 = "INSERT INTO reports (author, car_id, people_id, offence_id, fine_issued, points_issued, report_date, details) VALUES (?,?,NULL,?,NULL,NULL,?,?)";
                        $stmt1 = $conn->prepare($sql1);
                        $stmt1->bind_param("sssss", $author, $vehicle, $offence, $date, $details);
                        $stmt1->execute();

                        $data = array(
                            'error' => 'Report created!'
                        );

                        header('HTTP/1.0 200');
                        header('Content-Type: application/json');

                        echo json_encode($data);
                        die();

                    } else {

                        // if vehicle is empty Preparing the mysql query to insert all details
                        // of the report but only everything about the vehicle and fine and points is null.
                        // create the report and send a 200 response with a succession message.
                        if (empty($vehicle)){

                            $sql1 = "INSERT INTO reports (author, car_id, people_id, offence_id, fine_issued, points_issued, report_date, details) VALUES (?,NULL,?,?,NULL,NULL,?,?)";
                            $stmt1 = $conn->prepare($sql1);
                            $stmt1->bind_param("sssss", $author, $offender, $offence, $date, $details);
                            $stmt1->execute();

                            $data = array(
                                'error' => 'Report created!'
                            );

                            header('HTTP/1.0 200');
                            header('Content-Type: application/json');

                            echo json_encode($data);
                            die();

                        } else {

                            // Preparing the mysql query to insert all details but only fines and points are null.
                            // create the report and send a 200 response with a succession message.
                            $sql1 = "INSERT INTO reports (author, car_id, people_id, offence_id, fine_issued, points_issued, report_date, details) VALUES (?,?,?,?,NULL,NULL,?,?)";
                            $stmt1 = $conn->prepare($sql1);
                            $stmt1->bind_param("ssssss", $author, $vehicle, $offender, $offence, $date, $details);
                            $stmt1->execute();

                            $data = array(
                                'error' => 'Report created!'
                            );

                            header('HTTP/1.0 200');
                            header('Content-Type: application/json');

                            echo json_encode($data);
                            die();
                        }
                    }
                }
            }
        }
    }
}
