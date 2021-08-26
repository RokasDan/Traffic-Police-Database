<?php

// php script that adds new vehicle entry to the database.

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$plate = $data['plate'];
$brand = $data['brand'];
$model = $data['model'];
$colour = $data['colour'];
$owner = $data['owner'];

// Adding values for the database connection
require "server_connection.php";

// If plate or brand or colour is empty send a 400 response
// to the javascript file with an error message.
if (empty($plate) or empty($brand) or empty($colour)) {
    $data = array(
        'error' => 'Number plate, vehicle brand, or colour of the vehicle cant be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    // if plate value is longer than 10 symbols send a 400 response
    // to the javascript file with an error message.
    if (strlen($plate) > 10) {
        $data = array(
            'error' => 'Vehicle number plate cant be longer than 10 symbols!'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);

    } else {

        // if brand value is longer than 25 symbols send a 400 response
        // to the javascript file with an error message.
        if (strlen($brand) > 25) {
            $data = array(
                'error' => 'Vehicle brand name cant be longer than 25 symbols!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);

        } else {

            // if model value is longer than 20 symbols send a 400 response
            // to the javascript file with an error message.
            if (strlen($model) > 20) {
                $data = array(
                    'error' => 'Vehicle model cant be longer than 20 symbols!'
                );

                header('HTTP/1.0 400');
                header('Content-Type: application/json');

                echo json_encode($data);

            } else {

                // if colour value is longer than 20 symbols send a 400 response
                // to the javascript file with an error message.
                if (strlen($colour) > 20) {
                    $data = array(
                        'error' => 'Colour name cant be longer than 20 symbols!'
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);

                } else {

                    //Establish connection to mysql server.
                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    if (!$conn) {

                        // if connection fails send a 400 response
                        // to the javascript file with an error message.
                        $data = array(
                            'error' => 'Connection to server failed'
                        );

                        header('HTTP/1.0 400');
                        header('Content-Type: application/json');

                        echo json_encode($data);
                        die();

                    } else {

                        // Prepare and mysql statement which will check if a car
                        // with an inputted number plate already exist.

                        $sql2 = "SELECT number_plate FROM cars WHERE number_plate = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("s", $plate);
                        $stmt2->execute();
                        $result = $stmt2->get_result();

                        // if does exist send a 400 response
                        // to the javascript file with an error message.
                        if (mysqli_num_rows($result) != 0) {

                            $data = array(
                                'error' => 'Vehicle with that number plate already exists in the database!'
                            );

                            header('HTTP/1.0 400');
                            header('Content-Type: application/json');

                            echo json_encode($data);
                            die();

                        } else {

                            // If model is empty prepare insert query where this specific value is null.
                            // Insert the rest of the values and send 200 response to the javascript file
                            // with an succession message.
                            if (empty($model)) {

                                $sql1 = "INSERT INTO cars (number_plate, brand, model, colour, owner) VALUES (?,?,NULL,?,?)";
                                $stmt1 = $conn->prepare($sql1);
                                $stmt1->bind_param("ssss", $plate, $brand, $colour, $owner);
                                $stmt1->execute();

                                $data = array(
                                    'error' => 'Vehicle created!'
                                );

                                header('HTTP/1.0 200');
                                header('Content-Type: application/json');

                                echo json_encode($data);
                                die();

                            } else {

                                // If owner is empty prepare insert query where this specific value is null.
                                // Insert the rest of the values and send 200 response to the javascript file
                                // with an succession message.
                                if (empty($owner)) {

                                    $sql1 = "INSERT INTO cars (number_plate, brand, model, colour, owner) VALUES (?,?,?,?,NULL)";
                                    $stmt1 = $conn->prepare($sql1);
                                    $stmt1->bind_param("ssss", $plate, $brand, $model, $colour);
                                    $stmt1->execute();

                                    $data = array(
                                        'error' => 'Vehicle created!'
                                    );

                                    header('HTTP/1.0 200');
                                    header('Content-Type: application/json');

                                    echo json_encode($data);
                                    die();

                                } else {

                                    // If model and owner is empty prepare insert query where these specific values are null.
                                    // Insert the rest of the values and send 200 response to the javascript file
                                    // with an succession message.
                                    if (empty($owner) and empty($model)) {

                                        $sql1 = "INSERT INTO cars (number_plate, brand, model, colour, owner) VALUES (?,?,NULL,?,NULL)";
                                        $stmt1 = $conn->prepare($sql1);
                                        $stmt1->bind_param("sss", $plate, $brand, $colour);
                                        $stmt1->execute();

                                        $data = array(
                                            'error' => 'Vehicle created!'
                                        );

                                        header('HTTP/1.0 200');
                                        header('Content-Type: application/json');

                                        echo json_encode($data);
                                        die();

                                    } else {

                                        // Prepare insert query where with all values.
                                        // Insert the values and send 200 response to the javascript file
                                        // with an succession message.

                                        $sql1 = "INSERT INTO cars (number_plate, brand, model, colour, owner) VALUES (?,?,?,?,?)";
                                        $stmt1 = $conn->prepare($sql1);
                                        $stmt1->bind_param("sssss", $plate, $brand, $model, $colour, $owner);
                                        $stmt1->execute();

                                        $data = array(
                                            'error' => 'Vehicle created!'
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
        }
    }
}