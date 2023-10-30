<?php

/*
This PHP script appears to be a part of a web application, possibly serving as an API endpoint to retrieve data related to schools, classes, and subjects. Here's a breakdown of what this script does:

1. **Session Start**: It starts a PHP session. This can be used to maintain session data across multiple HTTP requests.

2. **School ID Assignment**: It assigns the value 9 to the `school_id` key in the session array.

3. **Include External Files**: The script includes two external PHP files: `connect.php` and `common_functions.php`. These files likely contain database connection details and common functions that will be used in this script.

4. **Set Content Type**: It sets the response content type to JSON, indicating that the script will return JSON data.

5. **Request Method Check**: It checks if the HTTP request method is POST. This script seems to be designed to handle POST requests.

6. **Retrieve Request Parameters**: It retrieves POST parameters from the request, including `classname`, `section_name`, and `mode`.

7. **Determine School Name**: It attempts to retrieve the school name based on the `school_id` by calling the `getSchoolNameFromSchoolId` function. It replaces spaces in the school name with underscores.

8. **Conditional Execution**: It contains conditional statements based on the value of the `mode` parameter. The script performs different actions based on the mode provided.

   - If `mode` is not "class," it retrieves subjects based on the class name and returns the data in a JSON response.
   - If `mode` is not "section," it retrieves class names in a specified section and their corresponding subjects and returns the data in a JSON response.

9. **JSON Response**: The script sends JSON responses with appropriate HTTP response codes, such as 200 for a successful response and 405 for invalid requests.

10. **Error Handling**: It handles cases where the school name is not found or when the HTTP method is not POST, returning appropriate error messages.

In summary, this script acts as an API endpoint that allows clients to retrieve information about classes and subjects in a school. The response is returned in JSON format. It uses external files for database connectivity and common functions to perform database queries and processing.

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
    // Define the action to perform
    $classname = $_POST['classname'];
    $section_name = $_POST['section_name'];
    $mode =  $_POST['mode'];



    $schoolName = getSchoolNameFromSchoolId($school_id, $conn);  
    if ($schoolName  !== null)
    {
        $schoolName = str_replace(" ","_",$schoolName);
        $class  =  $schoolName."_class";
        $subclass =  $schoolName."_subclass";
        $section =  $schoolName."_section";

                // Check if class name and section name are provided
                if ($mode  !== "class" ) {                    
                    $fetchsubject = array();
                    $classID = getClassIDFromClassname($classname, $conn, $class);
                    $fetchsubject =  fetchsubjectsfromclassID($conn,$subject,$classID);
                    if ($fetchsubject !==  null){
                        http_response_code(200);
                        echo json_encode(array('status' => true,'message' =>$fetchsubject));
                    } 
                } elseif ($mode !== "section") {
                    $classInSection  = array();
                    $classInSection = getAllClassNamesInSection($conn,$section_name,$section,$class);
                    //print_r($classInSection);
                    $countClass = count($classInSection);
                    if ($countClass > 0){
                        $fetchsubject =  array();
                        $hold_className = array();
                        foreach ($classInSection as $class_in_subclass){
                            // Get the classID based on the class name
                             $classID = getClassIDFromClassname($class_in_subclass, $conn, $class);
                            // IS The subclass name unique
                            $fetchsubject[] =  fetchsubjectsfromclassID($conn,$subject,$classID);
                            $hold_className[] =  $class_in_subclass;
                                     
                        }
                        $response['subject'] = json_encode($fetchsubject);
                        $response['class'] = json_encode($hold_className);
                        echo json_encode(array('status' => true,'message' =>$response));
                    }else{
                        echo json_encode(array('status' => false,'message' =>"There are no class in specified section."));      
                    }
            } else {
                // Invalid HTTP method
                http_response_code(405);
                echo json_encode(array('status' => false,'message' =>'Invalid request'));
            }
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
