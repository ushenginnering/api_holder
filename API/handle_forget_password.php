<?php
include "connect.php";
include "common_funtions.php";

// Function to generate a random verification code

function generateVerificationCode( $length = 6) {
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the user's email from the POST request
    $email = $_POST['email'];

    // Validate the email
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(array("status" => false,"message" => "Email is required."));
    } else {
        // Check if the email exists in the database
        $checkuserEmail =  checkusersEmail($conn,$email);


        if ($checkuserEmail == false) {
            http_response_code(500);
            echo json_encode(array("status" => false,"message" => "Error in SQL query: " . $conn->error));
        } else {
            if ($result->num_rows === 1) {
                // Generate a random verification code
                $code = generateVerificationCode();

                // Store the verification code in the database (you should create a new column for this)
               // $updateVerificationCodeSql = "UPDATE users SET verification_code = '$verificationCode' WHERE email = '$email'";
               $newotp =  updateOPTcode($conn,$code,$email) ;
                if ($newotp ==  true) {
                    
                    $send_mail = sendPaymentConfirmationEmail($conn,$code,$email);
                    if ($send_mail == true) {
                        http_response_code(200);
                    echo json_encode(array("status" => true,"message" => "Verification code sent to your email."));
                    } else if ($send_mail == false) {
                        http_response_code(500);
                        echo json_encode(array("status" => false,"message" => "Error storing verification code: " . $conn->error));
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(array("status" => false,"message" => "Error storing verification code: " . $conn->error));
                }
            } else {
                http_response_code(404);
                echo json_encode(array("status" => false,"message" => "Email not found."));
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
