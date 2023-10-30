<?php

/*
This PHP script appears to be an API endpoint for retrieving information about students in a school. Here's a breakdown of what the script does:

1. It starts a PHP session and sets a session variable `school_id` to 9.

2. It includes two external PHP files: 'connect.php' and 'common_functions.php', presumably for establishing a database connection and accessing common functions.

3. The script sets the response content type to JSON using the `header` function.

4. It checks if the HTTP request method is POST. The script is designed to handle POST requests.

5. If the request method is POST, it proceeds to process the request.

6. It retrieves various parameters from the POST request, including `adminNumber`, `classname`, `section_name`, and `mode`.

7. It uses the `getSchoolNameFromSchoolId` function to retrieve the school name based on the `school_id`. If the school name is found, it is used to construct table names for classes, subclasses, sections, and students.

8. Depending on the value of the `mode` parameter, the script performs different actions:

   - If `mode` is "student," it retrieves student details based on the `adminNumber`.

   - If `mode` is "class," it retrieves student details based on the class name (`classname`).

   - If `mode` is "section," it retrieves student details for all classes in the specified section.

9. The script then constructs a JSON response with either the student details or an error message, and sets the appropriate HTTP response code.

10. If the school name is not found or if the HTTP request method is not POST, it returns an error response with the corresponding status and message.

11. Finally, it closes the database connection using `mysqli_close`.

This script serves as an API endpoint to fetch student information based on different criteria, such as admin numbers, class names, and sections. It returns the data in JSON format, making it suitable for integration with other applications that need to retrieve student data from the specified school's database.
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
        // Get parameters from the request
        $studentID = $_POST['adminNumber'];
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
            $students = $schoolName."_students";

           //check mode to retrive student info
           if ($mode == "student"){
                $getstudentdetails =  array();
                $getstudentdetails =  getstudentsdetailsfromadminnumber($conn,$students,$studentID);
                if ($getstudentdetails !== null){              
                    http_response_code(200);
                    echo json_encode(array('status' => true,'message' => $getstudentdetails));
                }else{
                    http_response_code(405);
                    echo json_encode(array('status' => false,'message' =>'Student data not obtained'));
                }
           }
           else if  ($mode == "class"){
                $getstudentdetails =  array();
                $classID = getClassIDFromClassname($classname, $conn, $class);
                $getstudentdetails =  getstudentsdetailsfromclassID($conn,$students,$classID);
                if ($getstudentdetails !== null){              
                    http_response_code(200);
                    echo json_encode(array('status' => true,'message' => $getstudentdetails));
                }else{
                    http_response_code(405);
                    echo json_encode(array('status' => false,'message' =>'Student data not obtained'));
                }
           }
           else if  ($mode == "section"){

                $classInSection  = array();
                $classInSection = getAllClassNamesInSection($conn,$section_name,$section,$class);
                //print_r($classInSection);
                $countClass = count($classInSection);
                if ($countClass > 0){
                    $getstudentdetails = array();
                    $hold_className =  array();
                    foreach ($classInSection as $class_in_subclass){
                        // Get the classID based on the class name
                        $classID = getClassIDFromClassname($class_in_subclass, $conn, $class);
                        $getstudentdetails[] =  getstudentsdetailsfromclassID($conn,$students,$classID);
                        $hold_className[] =  $class_in_subclass;
                    }
                    $response['students'] = json_encode($getstudentdetails);
                    $response['class'] = json_encode($hold_className);
                    echo json_encode(array('status' => true,'message' =>$response));
                    
                }else{
                    echo json_encode(array('status' => false,'message' =>"There are no class in specified section."));      
                }


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

// Close the database connection
mysqli_close($conn);
?>
