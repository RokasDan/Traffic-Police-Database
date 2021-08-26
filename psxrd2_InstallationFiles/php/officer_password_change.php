<?php
// php script used to change the officer password.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$Username = $data['username'];
$Password = $data['password'];
$Officer = $data['name'];

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

    // If username or password is longer than 14 symbols send a 400 response
    // to the javascript file with an error message.
    if (strlen($Username) > 14 && strlen($Password) > 14) {

        $data = array(
            'error' => 'Username or Password is longer than 14 charters!'
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

            // Preparing the mysql query to check if password already exist.
            $sql = 'SELECT Password FROM officers WHERE Password = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $Username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_assoc($result);

            // If 0 results come back we check if password matches what user has entered.
            if (mysqli_num_rows($result) == 0) {

                if ($Username == $Password) {

                    // if password match change the old password with a new one.
                    // send a 200 response with a succession message.
                    $sql1 = 'UPDATE officers SET Password = ? WHERE Username = ?';
                    $stmt1 = $conn->prepare($sql1);
                    $stmt1->bind_param("ss", $Password, $Officer);
                    $stmt1->execute();

                    $data = array(
                        'error' => 'Password changed successfully!'
                    );

                    header('HTTP/1.0 200');
                    header('Content-Type: application/json');

                    echo json_encode($data);
                    die();

                } else {

                    // if passwords don match send a 400 response
                    // with an error message.
                    $data = array(
                        'error' => 'Passwords did not match!'
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);
                    die();
                }

            } else {

                    // if we receive results we send a 400 response with
                    // an error message.

                    $data = array(
                        'error' => 'Password already exists!'
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);
                    die();
            }
        }
    }
}
