<?php

// php script for adding a new offender to the database

// Decoding the json file received from a javascript file.
$data = json_decode(file_get_contents('php://input'), true);

// Splitting the json data in to separate values.
$firstname = $data['firstname'];
$lastname = $data['lastname'];
$address = $data['address'];
$licence = $data['licence'];
$dbo = $data['dbo'];

// Adding values for the database connection
require "server_connection.php";

// If firs name or last name or dbo is empty send a 400 response
// to the javascript file with an error message.
if (empty($firstname) or empty($lastname) or empty($dbo)) {
    $data = array(
        'error' => 'First name, last name, or date of birth cant be empty!'
    );

    header('HTTP/1.0 400');
    header('Content-Type: application/json');

    echo json_encode($data);

} else {

    //If address is longer than 150 symbols send a 400 response
    //to the javascript file with an error message.
    if (strlen($address) > 150) {
        $data = array(
            'error' => 'Address cant be longer than 150 symbols!'
        );

        header('HTTP/1.0 400');
        header('Content-Type: application/json');

        echo json_encode($data);

    } else {

        // if license is longer than 25 symbols send a 400 response
        //to the javascript file with an error message.
        if (strlen($licence) > 25) {
            $data = array(
                'error' => 'License number cant be longer than 25 symbols!'
            );

            header('HTTP/1.0 400');
            header('Content-Type: application/json');

            echo json_encode($data);

        } else {

            //IF lastname is longer than 20 symbols send a 400 response
            //to the javascript file with an error message.
            if (strlen($lastname) > 20) {
                $data = array(
                    'error' => 'Last name cant be longer than 20 symbols!'
                );

                header('HTTP/1.0 400');
                header('Content-Type: application/json');

                echo json_encode($data);

            } else {

                //IF firstname is longer than 20 symbols send a 400 response
                //to the javascript file with an error message.
                if (strlen($firstname) > 20) {
                    $data = array(
                        'error' => 'First name cant be longer than 20 symbols!'
                    );

                    header('HTTP/1.0 400');
                    header('Content-Type: application/json');

                    echo json_encode($data);

                } else {

                    // establishing connection to the data base.

                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    if (!$conn) {

                        // if connection fails send a 400 response to the javascript file
                        //with an error message.
                        $data = array(
                            'error' => 'Connection to server failed'
                        );

                        header('HTTP/1.0 400');
                        header('Content-Type: application/json');

                        echo json_encode($data);
                        die();

                    } else {

                        // IF address and licence empty prepare mysql insert query where these
                        // two specific values are null. Insert the rest values and send
                        // 200 response to the javascript file.
                        if (empty($address) and empty($licence)) {

                            $sql1 = "INSERT INTO people (first_name, last_name, address, date_of_birth, license_number) VALUES (?,?,NULL,?,NULL)";
                            $stmt1 = $conn->prepare($sql1);
                            $stmt1->bind_param("sss", $firstname, $lastname, $dbo);
                            $stmt1->execute();

                            $data = array(
                                'error' => 'Offender created!'
                            );

                            header('HTTP/1.0 200');
                            header('Content-Type: application/json');

                            echo json_encode($data);
                            die();

                        } else {

                            // IF address empty prepare mysql insert query where this
                            // value is null. Insert the rest values and send
                            // 200 response to the javascript file.
                            if (empty($address)) {

                                $sql2 = "INSERT INTO people (first_name, last_name, address, date_of_birth, license_number) VALUES (?,?,NULL,?,?)";
                                $stmt2 = $conn->prepare($sql2);
                                $stmt2->bind_param("ssss", $firstname, $lastname, $dbo, $licence);
                                $stmt2->execute();

                                $data = array(
                                    'error' => 'Offender created!'
                                );

                                header('HTTP/1.0 200');
                                header('Content-Type: application/json');

                                echo json_encode($data);
                                die();

                            } else {

                                // IF licence empty prepare mysql insert query where this
                                // value is null. Insert the rest values and send
                                // 200 response to the javascript file.
                                if (empty($licence)) {

                                    $sql2 = "INSERT INTO people (first_name, last_name, address, date_of_birth, license_number) VALUES (?,?,?,?,NULL)";
                                    $stmt2 = $conn->prepare($sql2);
                                    $stmt2->bind_param("ssss", $firstname, $lastname, $address, $dbo);
                                    $stmt2->execute();

                                    $data = array(
                                        'error' => 'Offender created!'
                                    );

                                    header('HTTP/1.0 200');
                                    header('Content-Type: application/json');

                                    echo json_encode($data);
                                    die();

                                } else {

                                    //Prepare a query to insert all of the values. Send a 200
                                    //response to the javascript file.

                                    $sql3 = "INSERT INTO people (first_name, last_name, address, date_of_birth, license_number) VALUES (?,?,?,?,?)";
                                    $stmt3 = $conn->prepare($sql3);
                                    $stmt3-> bind_param("sssss", $firstname, $lastname, $address, $dbo, $licence);
                                    $stmt3->execute();

                                    $data = array(
                                        'error' => 'Offender created!'
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

