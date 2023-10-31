<?php
include "connect.php";
require "common_funtions.php";

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user data from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the data
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "Email and password are required."));
    } else {
        // Retrieve user data from the database
        $checkemailstatus = checkusersEmail($conn, $email, $password);

        if ($checkemailstatus === false) {
            http_response_code(500);
            echo json_encode(array("status" => false, "message" => "Error in SQL query: " . $conn->error));
        
        } else {
            // Assuming $result is the result of a query
            if ($result->num_rows === 1) { 
                $user = $result->fetch_assoc();
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    http_response_code(200);
                    echo json_encode(array("status" => true, "message" => "Login successful"));
                    // You can also include additional user data in the response if needed.
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => false, "message" => "Invalid email or password"));
                }
            } else {
                http_response_code(401);
                echo json_encode(array("status" => false, "message" => "Invalid email or password"));
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("status" => false, "message" => "Method Not Allowed"));
}
?>
