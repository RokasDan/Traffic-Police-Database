<?php

// php script that updates the date of a specific report.

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
        'error' => 'Date can not be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // establish a connection to mysql server.

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {

        // if server connection fails send a 400 response
        // and an error message.
        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // prepare the mysql update query to update the values with
        // user provide ones. Update the values and send a 200 response
        // with a succession message.

        $sql = "UPDATE reports SET report_date = ? WHERE report_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $update);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array(
            'error' => 'Date updated'
        );

        header('HTTP/1.0 200');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    }
}