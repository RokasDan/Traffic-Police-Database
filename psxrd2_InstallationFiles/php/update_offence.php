<?php
// php script used to update offence type on a specific report.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$update= $data['update'];
$value = $data['value'];

// Adding values for the database connection
require "server_connection.php";

// If update is empty send a 400 response
// to the javascript file with an error message.
if (empty($update)) {
    $data = array(
        'error' => 'Not logged in!'
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

        //Preparing mysql query to update the offence type.
        //We run the query and send a 200 response with a succession message
        $sql = "UPDATE reports SET offence_id = ? WHERE report_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $update);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array(
            'error' => "Offence on report updated!"
        );

        header('HTTP/1.0 200');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    }
}
