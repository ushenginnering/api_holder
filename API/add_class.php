<?php

/*

This PHP script appears to be handling a POST request related to a school management system. Here's a breakdown of what the script is doing:

1. It starts a PHP session using `session_start()`.

2. It sets a session variable named 'school_id' to 9.

3. The script includes two other PHP files, 'connect.php' and 'common_functions.php', which presumably contain database connection code and common functions used in the script.

4. It sets the content type of the response to JSON using the 'header' function.

5. The script checks if the HTTP request method is POST.

6. If the request is a POST request, it proceeds with the following actions:

   a. It retrieves the 'class_name' and 'section' data from the POST request.

   b. It calls the 'getSchoolNameFromSchoolId' function to fetch the school name associated with the 'school_id'.

   c. If the school name is found, it transforms the school name to a format suitable for database table names.

   d. It checks if the 'section' name is provided in the POST data.

   e. If the 'section' name is provided, it sanitizes and escapes the input data to prevent SQL injection.

   f. It calls the 'getSchoolsectionIdFromSchoolsectonname' function to retrieve the section ID based on the section name and school-specific table name.

   g. It checks if the section ID is found and not empty.

   h. It calls the 'isClassNameUnique' function to determine if the class name is unique in the specified class table.

   i. If the class name is unique, it inserts the class data into the appropriate class table with the section ID.

   j. It sends a JSON response with the status and a message indicating the success or failure of the operation.

7. If any of the conditions fail during this process (e.g., school name not found, section name not provided, class name not unique), it sends a JSON response with an error message.

8. Finally, it closes the database connection using `$conn->close()`.

9. If the request method is not POST, it responds with a 405 (Method Not Allowed) HTTP status code and a JSON response indicating an invalid HTTP method.

This script is responsible for adding a class to a school management system's database and providing JSON responses to indicate the success or failure of the operation, including error messages in case of any issues.

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
 
    // Get the class name and section from the request
     $className = $_POST['class_name'];
     $sectionName = $_POST['section'];

    $schoolName = getSchoolNameFromSchoolId($school_id, $conn);  
    if ($schoolName  !== null)
    {
        $schoolName = str_replace(" ","_",$schoolName);
         $class  =  $schoolName."_class";
         $section = $schoolName."_section";

        // Check if section name is provided
        if (!empty($sectionName)) {
            // Escape and sanitize the input data
            $className = mysqli_real_escape_string($conn, $className);
            $sectionName = mysqli_real_escape_string($conn, $sectionName);

            $sectionId = getSchoolsectionIdFromSchoolsectonname($sectionName, $conn,$section);

            if ($sectionId !== null && $sectionId !== ""){

                $uniqueclass = isClassNameUnique($conn, $className, $class);

                if ($uniqueclass === "true"){
                    // Insert the class data into the 'class' table with the section ID
                    $insertClassSql = "INSERT INTO $class (class_name, class_section_id) VALUES ('$className', $sectionId)";

                    if ($conn->query($insertClassSql)) {
                        // Class added successfully
                        $response = array('status' => true,'message' => 'Class added successfully');
                        echo json_encode($response);
                    } else {
                        // Error occurred while adding the class
                        $response = array('status' => false,'message' => 'Error adding class');
                        echo json_encode($response);
                    }                    
                }else{
                         // Error occurred class name not unique
                         $response = array('status' => false,'message' => 'Classname not unique');
                         echo json_encode($response);                   
                }

                                        
            }else{
                // Section name not provided
                $response = array('status' => false,'message' =>'Section id not found');
                echo json_encode($response);               
            }
        } else {
            // Section name not provided
            $response = array('status' => false,'message' =>'Section name not provided');
            echo json_encode($response);
        }
    }  
    else {
            // Section name not found
            $response = array('status' => false,'message' => 'School name not found');
            echo json_encode($response);
        }

    // Close the database connection
    $conn->close();
} else {
    // Invalid HTTP method
    http_response_code(405);
    echo json_encode(array('status' => false,'message' =>'Invalid HTTP method'));
}

?>