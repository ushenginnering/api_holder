<?php

/*
This PHP script appears to be responsible for deleting a class and its associated subclass records from a database. Here's a breakdown of what this script does:

1. It starts a PHP session and assigns a value of 9 to the 'school_id' session variable.

2. It includes two external PHP files, 'connect.php' and 'common_functions.php'. 'connect.php' is likely used to establish a database connection, and 'common_functions.php' is expected to contain common functions used in this script.

3. It retrieves the school name associated with the school ID (in this case, 9) by calling the 'getSchoolNameFromSchoolId' function from 'common_functions.php'. It checks if the retrieved school name is not empty and not null.

4. If the school name is valid, it proceeds to generate table names for the class, subclass, and section based on the school name.

5. It checks if the HTTP request method is POST. If it's not a POST request, it responds with a 405 (Method Not Allowed) error along with a message.

6. If it is a POST request, the script proceeds to process the request:

   a. It checks if the 'className' parameter is set in the request. If it's not set, it responds with a 400 (Bad Request) error, indicating that the 'classname' parameter is missing.

   b. If the 'className' parameter is provided, it retrieves the class ID by calling the 'getClassIDFromClassname' function, which likely queries the database using the class name and 'class' table name.

   c. If the class ID is found (not null), it attempts to delete the class and its associated subclass records using prepared statements by calling 'delete_class_by_classID' and 'delete_sub_class_from_classID' functions.

   d. Depending on the success or failure of these deletion operations, it responds with appropriate HTTP status codes and JSON-encoded messages. If both deletions are successful, it returns a 200 (OK) status along with a success message. If there are any failures, it responds with a 500 (Internal Server Error) status and an error message.

7. If the school name is not found (schoolName is empty or null), the script responds with a 405 (Method Not Allowed) status and an error message indicating that the school was not found.

In summary, this script is designed to delete a class and its associated subclass records from a database. It performs this operation in response to a POST request that includes the 'className' parameter. It provides appropriate status codes and messages for success and failure scenarios.
*/

session_start();
$school_id = $_SESSION['school_id'] = 9;
// Include the database connection
include 'connect.php';
include 'common_funtions.php';



$schoolName = getSchoolNameFromSchoolId($school_id, $conn);
if ($schoolName  !== "" && $schoolName !== null )
{
     $schoolName = str_replace(" ","_",$schoolName);
    $class  =  $schoolName."_class";
    $subclass =  $schoolName."_subclass";
    $section =  $schoolName."_section";


    // Check if the request is a DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Parse the request body as JSON
            // $_POST['classname'];
            // Check if the "classname" parameter is provided
            if (isset($_POST['className'])) {
               $classname = $_POST['className'];
                
                // Get the classID based on the class name
                 $classID = getClassIDFromClassname($classname, $conn, $class);
            
                if ($classID !== null) {

                    // Use prepared statements to delete the class row
                    $delete_class =  delete_class_by_classID($conn, $class,$classID);

                    if ($delete_class == "true") {
                        $delete_sub_class  =  delete_sub_class_from_classID($conn,$subclass,$classID);
                        if ($delete_sub_class == "true") {
                            http_response_code(200);
                            echo json_encode(array('status' => true,'message' => "Class and associated subclass records deleted."));
                        } else {
                            http_response_code(500);
                            echo json_encode(array('status' => false,'message' => "Failed to delete associated subclass records."));
                        }
                    } else {
                        http_response_code(500);
                        echo json_encode(array('status' => false,'message' => "Failed to delete class."));
                    }
            }else{
                http_response_code(400);
                echo json_encode(array('status' => false,'message' => "class name not found."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('status' => false,'message' => "Missing 'classname' parameter."));
        }
    } else {
        http_response_code(405);
        echo json_encode(array('status' => false,'message' => "Invalid request method. Use DELETE."));
    }
}
else {
    http_response_code(405);
    echo json_encode(array('status' => false,'message' => "School not founf"));
}
?>
