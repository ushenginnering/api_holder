<?php
// Include the database connection file
include "connect.php";
require "common_funtions.php";

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user data from the POST request
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];
    $resetPasswordToken = $_POST['reset_password_token'];

    // Validate the data
    if (empty($email) || empty($newPassword) || empty($resetPasswordToken)) {
        http_response_code(400);
        echo json_encode(array("message" => "Email, new password, and reset password token are required."));
    } else {
        // Check if the reset password token is valid
        $checkTokenSql = "SELECT * FROM password_reset_tokens WHERE email = '$email' AND token = '$resetPasswordToken'";
        $result = $conn->query($checkTokenSql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(array("message" => "Error in SQL query: " . $conn->error));
        } else {
            if ($result->num_rows === 1) {
                // Token is valid, update the user's password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updatePasswordSql = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
                if ($conn->query($updatePasswordSql) === TRUE) {
                    // Password updated, remove the reset token
                    $deleteTokenSql = "DELETE FROM password_reset_tokens WHERE email = '$email'";
                    $conn->query($deleteTokenSql);
                    http_response_code(200);
                    echo json_encode(array("message" => "Password updated successfully"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Error updating password: " . $conn->error));
                }
            } else {
                http_response_code(401);
                echo json_encode(array("message" => "Invalid or expired reset password token"));
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
