<?php
/*
This PHP script appears to be a server-side script designed to handle a specific API request related to managing the order of classes in a school. Here's a short explanation of what this script does:

1. It starts a PHP session and sets a session variable `school_id` to 9.

2. It includes two external PHP files, 'connect.php' and 'common_functions.php', which likely contain functions and configurations needed for the script's operation.

3. It sets the response content type to JSON by sending an HTTP header with the content type 'application/json'.

4. It checks if the HTTP request method is POST. This script is intended to handle POST requests.

5. It retrieves parameters from the POST request: `className`, `subjectName`, `sectionName`, and `mode`.

6. It then attempts to get the school name corresponding to the `school_id` using the `getSchoolNameFromSchoolId` function from the included files.

7. If the school name is found and not null, it is modified to replace spaces with underscores, and the class name is constructed using this modified school name.

8. The script checks if the request method is POST again, although this check is redundant since it has already been checked earlier.

9. If the 'class_name_array' parameter is provided and is an array in the POST request, it proceeds to update the class order based on the provided array. It appears to use a function called `UpdateClassOrder` to update the class order. If the update is successful, it responds with a JSON-encoded success message. If not, it responds with an error message.

10. If 'class_name_array' is not provided in the request, it responds with an error message and an HTTP 400 (Bad Request) status code.

11. If the request method is not POST, it responds with an error message and an HTTP 405 (Method Not Allowed) status code, indicating that only POST requests are accepted.

12. After processing the request, the script closes the database connection.

13. If the school name is not found (invalid `school_id`), it responds with an HTTP 405 status code and a message indicating that the school was not found.

14. If the HTTP request method is not POST, it responds with an HTTP 405 status code and a message indicating an invalid HTTP method.

In summary, this script is designed to handle POST requests for updating the order of classes in a school. It retrieves data from the POST request, processes it, and responds with JSON-encoded success or error messages. It also performs checks for valid HTTP methods and handles errors related to school not found or invalid HTTP methods.
*/
session_start();
$school_id = $_SESSION['school_id'] = 9;

// Include the database connection
include 'connect.php';
include 'common_funtions.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get parameters from the request
    $classname = $_POST['className'];
    $subjectname = $_POST['subjectName'];
    $sectionname = $_POST['sectionName'];
    $mode = $_POST['mode']; 

    $schoolName = getSchoolNameFromSchoolId($school_id, $conn);  
    if ($schoolName  !== null)
    {
        $schoolName = str_replace(" ","_",$schoolName);
        $class  =  $schoolName."_class";

        // Check if the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the JSON data sent in the request body
    
            // Check if the 'class_name_array' parameter is provided
            if (isset($_POST['class_name_array']) && is_array($_POST['class_name_array'])) {
                $ordered_class_array = array();
                $ordered_class_array =  $_POST['class_name_array'];
                // Prepare a SQL statement to update class order based on class name
                $UpdateClassOrder = UpdateClassOrder($conn,$ordered_class_array,$class);
                if ($UpdateClassOrder !== null){
                    // Provide a success response
                    $response = array('status' => 'success', 'message' => '');
                    echo json_encode($response);                    
                }else{
                    $response = array('status' => 'false', 'message' => 'Class order updated successfully.' );
                    echo json_encode($response);                      
                }

            } else {
                // Provide an error response if 'class_name_array' is not provided
                $response = array('status' => 'error', 'message' => 'Missing or invalid parameters.');
                http_response_code(400); // Bad Request
                echo json_encode($response);
            }
        } else {
            // Provide an error response for non-POST requests
            $response = array('status' => 'error', 'message' => 'Invalid request method. Use POST.');
            http_response_code(405); // Method Not Allowed
            echo json_encode($response);
        }

        // Close the database connection
        $conn->close();
    }else{
        // Invalid HTTP method
        http_response_code(405);
        echo json_encode(array('status' => false,'message' =>'School not found'));
    }
}else{
 // Invalid HTTP method
http_response_code(405);
echo json_encode(array('status' => false,'message' =>'Invalid HTTP method'));
}


?>
