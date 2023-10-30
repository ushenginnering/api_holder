<?php


/*
This PHP script appears to be a part of a web application that handles the creation of school and user data. Let's break down the key components and functionality of the script:

1. **Including External Files**: The script starts by including two external files, "connect.php" and "common_functions.php." These files likely contain necessary functions, database connections, or configurations used by this script.

2. **Initializing a Response Array**: The script initializes an empty array called `$response`, which will be used to construct and return a JSON response to the client.

3. **Parameter Checking**: The script checks if specific parameters have been provided via a POST request. These parameters include details about a school, such as its name, administrator name, address, phone, email, logo location, motto, influencer ID, and password.

4. **Data Processing**: If all the required parameters are provided, the script proceeds to process the data. It does the following:

   - It extracts the provided data and sets them as variables.
   - It generates academic session and term data.
   - It hashes the user-provided password using PHP's `password_hash` function for secure storage.
   - It creates tables related to the school using a function `create_tables`.
   - It attempts to retrieve the school's ID from the database.
   - It checks if the school name is unique. If not, it sets an error response.
   - If the school name is unique, it proceeds to create a school and a user in the database. The user is assigned the "Admin" role.

5. **Database Operations**: The script appears to rely on functions such as `create_school`, `save_user`, and `get_this_school_id_by_name` to interact with the database for creating school and user data.

6. **Response Construction**: The script constructs a response array, setting the 'status' and 'message' keys based on the success or failure of the database operations. If everything is successful, it sets 'status' to true and provides a success message.

7. **Missing Data Handling**: If any of the required parameters are missing, the script sets the 'status' to false and provides a "Missing data" message.

8. **JSON Response**: The script closes the database connection, specifies the response format as JSON using the `header` function, and echoes the JSON-encoded response array back to the client.

Overall, this script serves as an endpoint for creating school and user data in a database. It performs data validation, database operations, and returns JSON responses to indicate the outcome of these operations, such as the successful creation of school and user data or the presence of missing data.
*/





include "connect.php";
require "common_funtions.php";

$response = array(); // Initialize a response array

// Check if all required parameters are provided
if (
    isset($_POST['school_name']) &&
    isset($_POST['school_admin_name']) &&
    isset($_POST['school_address']) &&
    isset($_POST['school_phone']) &&
    isset($_POST['school_email']) &&
    isset($_POST['school_logo_location']) &&
    isset($_POST['school_moto']) &&
    isset($_POST['school_influencer_id']) &&
    isset($_POST['school_password'])
) {
    
    // All required parameters are provided
    $school_name = $_POST['school_name'];
    $school_admin_name = $_POST['school_admin_name'];
    $address = $_POST['school_address'];
    $phone = $_POST['school_phone'];
    $email = $_POST['school_email'];
    $current_session = generate_academic_session();
    $term = generate_academic_term();
    $logo_location = $_POST['school_logo_location'];
    $moto = $_POST['school_moto'];
    $influencerId = $_POST['school_influencer_id'];
    // Password provided by the user
    $userProvidedPassword = $_POST['user_password'];

    // Hash the user's password
    $school_password = password_hash($userProvidedPassword, PASSWORD_DEFAULT);

    // Create other school-related tables
    $create_tables = create_tables($conn, $school_name);
       
    // Get the school ID
    $schoolID = get_this_school_id_by_name($conn, $school_name);

    // Check if school name is unique
    if ($schoolID != null) {
        $response['status'] = false;
        $response['message'] = "School name is not unique.";
    } else {
        $status = "Admin";
        $signature = "";

         // Tables created successfully
        // Call the create_school function to insert school data
        $create_school =  create_school($conn, $school_name, $address, $phone, $email, $current_session, $term, $logo_location, $moto, $influencerId, $school_password);
        // Save user
        $schoolID = get_this_school_id_by_name($conn, $school_name);

        $create_user = save_user($conn, $school_admin_name, $email, $phone, $schoolID, $status, $signature, $school_password);

      //  $response['create_user_status'] = $create_user;
       // $response['create_tables_status'] = $create_tables;
       // $response['create_school_status'] = $create_school;
        $response['status'] = true;
        $response['message'] = "School and user data created successfully.";
       
    }
} else {
    // Missing data
    $response['status'] = false;
    $response['message'] = "Missing data.";
}

// Close the database connection
mysqli_close($conn);

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
