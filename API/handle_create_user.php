<?php
// Include the database connection file
// Include the database connection
include 'connect.php';
include 'common_funtions.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user data from the POST request
    $email = $_POST['email'];
    $username = $_POST['userName'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $status = $_POST['position'];
    $schoolName = $_POST['school_name'];
    if ($schoolName !== '') {
    $schoolID = get_this_school_id_by_name($conn, $schoolName);
    } else {
        $schoolID = '';
    }


    // Validate the data
    if (empty($email) || empty($password) || empty($confirmPassword) || empty($status) || empty($username)) {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "All fields are required."));
    } elseif ($password !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "Password and Confirm Password do not match."));
    } else {
        // Hash the password (you should use a more secure method)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user data into the database
       $create_user = save_user($conn, $username, $email, '', $schoolID, $status, $signature, $school_password);
        if ($create_user == "User data inserted successfully.<br>") {
            http_response_code(201);
            echo json_encode(array("status" => true,"message" => "User created successfully."));
        } else {
            http_response_code(500);
            echo json_encode(array("status" => false,"message" => "Error creating user: " . $conn->error));
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("status" => false,"message" => "Method Not Allowed"));
}
?>
