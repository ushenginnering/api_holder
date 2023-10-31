<?php
// Include the database connection file
include "connect.php";
require "common_funtions.php";

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data from the POST request
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $school_name = $_POST['school_name'];
    $email = $_POST['email'];
    $capacity_ranges = $_POST['capacity_ranges'];
    $referral_source = $_POST['referral_source'];
    $desired_results = $_POST['desired_results'];

    // Validate the data
    if (empty($first_name) || empty($last_name) || empty($school_name) || empty($capacity_ranges) || empty($referral_source) || empty($desired_results) || empty($email)) {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "All fields are required."));
    } else {
        // Insert the form data into the database (using prepared statements to prevent SQL injection)
        $insertDataSql = "INSERT INTO contact_requests (first_name, last_name, school_name, capacity_ranges, referral_source, desired_results, email) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insertDataSql);
        $stmt->bind_param("sssssss", $first_name, $last_name, $school_name, $capacity_ranges, $referral_source, $desired_results, $email);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("status" => true, "message" => "Form data saved successfully."));
        } else {
            http_response_code(500);
            echo json_encode(array("status" => false, "message" => "Error saving form data: " . $conn->error));
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("status" => false, "message" => "Method Not Allowed"));
}
?>
