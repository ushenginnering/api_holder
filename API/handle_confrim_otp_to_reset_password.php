<?php

// Check for the incoming request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieving the data from the POST request
    $email = $_POST['email'];
    $otp = $_POST['otp']; // The OTP entered by the user

    // Simulating the OTP validation (Replace this with your own validation logic)
    $storedOTP = getStoredOTP($conn, $email); // Example: The stored OTP sent to the user

    if ($otp === $storedOTP) {
        // OTP is correct
        http_response_code(200);
        echo json_encode(array("status" => true, "message" => "OTP verification successful.", "action"=> "redirect to reset password"));
    } else {
        // OTP is incorrect
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "Invalid OTP."));
    }
} else {
    // If the request method is not POST
    http_response_code(405);
    echo json_encode(array("status" => false, "message" => "Method Not Allowed"));
}

?>
