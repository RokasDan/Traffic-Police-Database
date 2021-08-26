<?php

// php script used to store details about new admins

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

    // If username and password is not bigger than 14 symbols
    // establish the connection to the mysql server.

    if (strlen($Username) < 14 && strlen($Password) < 14) {

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {

            // If connection fails send a 400 response
            // to the javascript file with an error message.

            $data = array(
                'error' => 'Connection to server failed'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);
            die();

        } else {

            // preparing mysql query to check if the password and user
            // name already exist.

            $sql1 = "SELECT Username FROM officers WHERE Username = ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("s", $Username);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $row1 = mysqli_fetch_assoc($result1);

            $sql2 = "SELECT Password FROM officers WHERE Password = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("s", $Password);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $row2 = mysqli_fetch_assoc($result2);

            //if even one of the query's returns a result send a 400 response
            // to the javascript file with an error message.

            if (mysqli_num_rows($result1) != 0 || mysqli_num_rows($result2) != 0) {

                $data = array(
                    'error' => 'Username Or Password already exist!'
                );

                header('HTTP/1.0 400');
                header('Content-Type: application/json');

                echo json_encode($data);
                die();

            } else {

                // inserting details of a new admin in to the database!

                $sql3 = "INSERT INTO officers (Username, Password) VALUES (?,?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("ss", $Username, $Password);
                $stmt3->execute();

                $data = array(
                    'error' => 'User Created!'
                );

                header('HTTP/1.0 200');
                header('Content-Type: application/json');

                echo json_encode($data);
                die();

            }
        }

    } else {

        // If username or password is bigger than 14 symbols send 400 response
        // to the javascript file with an error message.

        $data = array(
            'error' => 'Username Or Password is longer than 14 characters!'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);
        die();

    }
}
