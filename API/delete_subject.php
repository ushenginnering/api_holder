<?php
/*
This PHP script appears to be a server-side script designed to handle a specific type of HTTP request, possibly from a web or mobile application. Here's a brief explanation of what this script does:

1. **Session Initialization**: The script starts a PHP session using `session_start()` and assigns a value of `9` to the `school_id` session variable. This suggests it is working with user sessions and storing a school ID.

2. **Including Dependencies**: It includes two external PHP files, 'connect.php' and 'common_functions.php', which presumably contain necessary functions and database connection information.

3. **Content Type Declaration**: It sets the response content type to JSON using `header('Content-Type: application/json')`. This means that the script will return JSON data in its response.

4. **Request Method Check**: It checks whether the HTTP request method is POST. This script appears to handle HTTP POST requests.

5. **Request Data Retrieval**: It retrieves several parameters from the POST request, including `className`, `subjectName`, `sectionName`, and `mode`.

6. **Database Interaction**: It interacts with a database to perform various operations, which may include:

   - Getting the school name based on the `school_id`.
   - Constructing table names (e.g., `$class`, `$section`, `$subject`) based on the school name.
   - Deleting subjects based on class and subject name.
   - Retrieving class IDs based on class names.
   - Handling different modes of operation (`class`, `subject`, or `section`) and performing corresponding actions.
   - Handling cases where a subject does not exist in a particular class.

7. **Response Handling**: The script responds with JSON-encoded data depending on the outcome of the database operations and the logic within the script. Responses include status messages, error codes (HTTP response codes), and data in JSON format.

8. **Error Handling**: It handles various error scenarios, setting appropriate HTTP response codes (e.g., 400 for Bad Request, 404 for Not Found, and 500 for Internal Server Error) and providing error messages in the response.

9. **Invalid HTTP Method**: If the request method is not POST, the script responds with an HTTP 405 error, indicating an invalid HTTP method.

In summary, this script is designed to handle POST requests related to managing subjects in classes, and it interacts with a database to perform these operations. It returns JSON responses to the client, indicating the success or failure of the requested operations.
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
         $section = $schoolName."_section";
         $subject = $schoolName."_subject";

        // Check if the required parameters are provided
        if (empty($classname) && empty($sectionname)) {
            // Return an error response if the parameters are incomplete
            http_response_code(400); // Bad Request
            echo json_encode(['status' => false,'message' => "Incomplete parameters"]);
            exit;
        }
       
        if (!empty($subjectname)) {
            // Delete subjects based on class name
            $classId =  getClassIDFromClassname($classname, $conn, $class);
      
            if ($classId !== null) {
                if ($mode === 'class') {
                    // Delete all subjects for the class
                  echo   $delete_all_subject =  deleteSubjectsByClassID($conn, $classId,'',$subject);
                    if ($delete_all_subject ==  "true") {
                        echo json_encode(['status' => false,'message' =>  "All subjects in class '$classname' deleted"]);
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(['status' => false,'message' =>  "Failed to delete subjects"]);
                    }
                } else if ($mode == "subject") {
                    // Delete a specific subject for the class
                    $delete_all_subject =  deleteSubjectsByClassID($conn, $classId,$subjectname,$subject);
                    if ($delete_all_subject  === "true") {
                        echo json_encode(['status' => false,'message' => "Subject '$subjectname' in class '$classname' deleted"]);
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(['status' => false,'message' => "Failed to delete subject"]);
                    }
                } else if ($mode == "section"){
                    $classInSection  = array();
                    $classInSection = getAllClassNamesInSection($conn,$sectionname,$section,$class);

                    $countClass = count($classInSection) ;
                    if ($countClass > 0){
                        $report_holder =  array();
                        //print_r($classInSection);
                        for ($p= 0; $p <= $countClass; $p++)
                        {
                            
                             $classInSection[$p];
                            // Get the classID based on the class name
                             $classID = getClassIDFromClassname($classInSection[$p], $conn, $class);
                            $uniqueSubject =  issubjectUniqueinClass($conn,$subject,$subjectname,$classID);
                            if ($uniqueSubject === "false"){
                                  $report_holder[] =  deleteSubjectsByClassID($conn, $classID,$subjectname,$subject);
                            }else{
                                $report_holder[] =  "subject does not exist in class";
                            }
                         }
                        $response = json_encode($report_holder);
                        echo json_encode(array('status' => true,'message' =>$response)); 
                    }
                }
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['status' => false,'message' =>  "Class '$classname' not found"]);
            }
        }
    }else {
              // Section name not found
              $response = array('status' => false,'message' => 'School name not found');
              echo json_encode($response);
    }
}else{
 // Invalid HTTP method
 http_response_code(405);
 echo json_encode(array('error' => 'Invalid HTTP method'));
}
?>
