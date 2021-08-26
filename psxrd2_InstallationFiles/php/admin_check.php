<?php

// This php script is used to authenticate if the admin
//exists in the data base.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Username = $data['username'];
$Password = $data['password'];

// Adding values for the database connection
require "server_connection.php";

// If username or password is empty send a 400 response
// to the javascript file with an error message.
if (empty($Username) || empty($Password)) {
    $data = array(
        'error' => 'Username and password cant be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // establishing connection to the database

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {

        // if connection fails send 400 response to the javascript file
        //with an error message and kill the connection.

        $data = array(
            'error' => 'Connection to server failed'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    } else {

        // Preparing the mysql query to find a password that belongs
        // to this specific user name.

        $sql = "SELECT Password FROM admins WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) == 0) {

            // If no results received send 400 response with an error message.

            $data = array(
                'error' => 'Username Or Password is wrong!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // if password variable is == to the received password
            // row send a 200 response with a secret message.

            if ($Password == $row['Password']) {

                $data = array(
                    'secret' => 'super secret'
                );

                header('HTTP/1.0 200');
                header('Content-Type: application/json');

                echo json_encode($data);

            } else {

                // If the password doesnt match with the row
                // send a 400 response with an error message.

                $data = array(
                    'error' => 'Username Or Password is wrong!'
                );

                header('HTTP/1.0 400');
                header('Content-Type: application/json');

                echo json_encode($data);
                die();

            }
        }
    }
}

