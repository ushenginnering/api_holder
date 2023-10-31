<?php
// Include the database connection file
include 'connect.php';
include 'common_funtions.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user data from the POST request
    $email = $_POST['email'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password']; // Corrected variable name

    // Validate the data
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "Email, new password, and confirm password are required."));
    } else {
        if ($newPassword !== $confirmPassword) {
            http_response_code(400); // Corrected HTTP status code
            echo json_encode(array("status" => false, "message" => "New password and confirm password do not match."));
        } else {
            // Update password
            $updatePass = updatePassword($conn, $newPassword, $email); // Fixed variable name $password to $newPassword

            if ($updatePass === true) {
                http_response_code(200);
                echo json_encode(array("status" => true, "message" => "Password updated successfully"));
            } else {
                http_response_code(500);
                echo json_encode(array("status" => false, "message" => "Error updating password: " . $conn->error));
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("status" => false, "message" => "Method Not Allowed"));
}
?>
