<?php

// php script which updates the description of a specific report
// provided by a an officer or an admin.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$update= $data['update'];
$value = $data['value'];

// Adding values for the database connection
require "server_connection.php";

// If update or value is empty send a 400 response
// to the javascript file with an error message.
if (empty($update) || empty($value)) {
    $data = array(
        'error' => 'New description cant be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // if value string is longer than 150 symbols send 400
    // response and an error message.

    if (strlen($value) >150) {
        $data = array(
            'error' => 'New description cant be longer than 150 symbols!'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);

    } else {

        // establish a connection to mysql server.
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {

            // if connection fails send a 400 response
            // with an error response.
            $data = array(
                'error' => 'Connection to server failed'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // prepare mysql update query. Update the database with
            // the ser input. Send a 200 response with a succession
            // message.

            $sql = "UPDATE reports SET details = ? WHERE report_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $value, $update);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = array(
                'error' => 'Report updated!'
            );

            header('HTTP/1.0 200');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        }
    }
}