
<?php
/*
This PHP script appears to be a part of a web application that manages subjects in a school. Here's a breakdown of what the script does:

1. **Session Start**: The script starts a session, which is a way to store and retrieve user data across multiple pages.

2. **Setting School ID in Session**: It sets a `school_id` in the user's session as 9. This might be used to keep track of the school for which data is being managed.

3. **Including Files**: The script includes two PHP files, 'connect.php' and 'common_functions.php'. These files likely contain functions and configurations necessary for the script's operation.

4. **Setting Content Type**: It sets the content type of the response to JSON, indicating that the script will respond with JSON data.

5. **Retrieving School Name**: It retrieves the school name from the school ID using a function called `getSchoolNameFromSchoolId`.

6. **Checking Request Method**: The script checks if the HTTP request method is POST. It proceeds only if it's a POST request.

7. **Validating and Handling POST Data**:
   - It checks if the required POST parameters, namely 'className', 'subjectName', 'sectionName', and 'mode', are provided.
   - If 'mode' is 'class', it looks for the class ID based on the provided class name and adds a subject to that class.
   - If 'mode' is 'section', it handles multiple classes in a section and adds a subject to each of them.
   - It returns JSON responses with success or failure messages.

8. **Error Handling**: If any required parameters are missing or if the school name or class name is not recognized, it returns appropriate error messages.

9. **Closing Database Connection**: It closes the database connection after processing the request.

10. **Handling Invalid HTTP Methods**: If the request method is not POST, it returns an error message with HTTP status code 405 (Method Not Allowed).

11. **Handling Invalid School ID**: If the school ID is not recognized, it returns an error message with an HTTP status code 405.

In summary, this script is part of a web application for managing subjects in a school. It handles requests to add subjects to classes or sections, and it responds with JSON data to indicate success or failure. The script also performs basic error checking and handles different scenarios based on the 'mode' provided in the POST data.

*/

session_start();

$school_id = $_SESSION['school_id'] = 9;

// Include the database connection
include 'connect.php';
include 'common_funtions.php';

// Set the content type to JSON
header('Content-Type: application/json');


$schoolName = getSchoolNameFromSchoolId($school_id, $conn);  
if ($schoolName  !== null)
{
    $schoolName = str_replace(" ","_",$schoolName);
     $class  =  $schoolName."_class";
     $subject = $schoolName."_subject";
     $section = $schoolName."_section";


    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Check if the required parameters are provided
        if (isset($_POST['className']) && isset($_POST['subjectName']) && isset($_POST['sectionName'])) {
            // Get the provided parameters
            $classname = $_POST['className'];            
            $sectionname = $_POST['sectionName'];
            $mode  = $_POST['mode'];
            $subjectname = $_POST['subjectName'];


            if (!empty($classname) and $mode === "class") {
                // Check the 'class' table to find the class ID based on the class name
                $classId =  getClassIDFromClassname($classname, $conn, $class);
                if ($classId !== null && $classId !== "") {
                    $uniqueSubject =  issubjectUniqueinClass($conn,$subject,$subjectname,$classId);
                    if ($uniqueSubject == "true")
                    {
                        $add_subject_to_class =  add_subject_to_class_id($conn,$subject,$subjectname,$classId);
                        if ($add_subject_to_class === "true"){
                            echo json_encode(array('status' => true,'message' => 'subject added to class'));
                        }else{
                            echo json_encode(array('status' => false,'message' => 'failed to add subject added to class'));
                        }
                    }else{
                        echo json_encode(array('status' => false,'message' => 'subject added to class already'));
                    }

                } else {
                    echo json_encode(array('status' => false,'message' => 'class name not recognized'));
                }
            } elseif (!empty($sectionname) && $mode  === "section" ) {
                $classInSection  = array();
                $classInSection = getAllClassNamesInSection($conn,$sectionname,$section,$class);
                //print_r($classInSection);
                $countClass = count($classInSection);
                if ($countClass > 0){
                    $report_holder =  array();
                    foreach ($classInSection as $class_in_subclass){
                        // Get the classID based on the class name
                       echo  $classID = getClassIDFromClassname($class_in_subclass, $conn, $class);
                         $uniqueSubject =  issubjectUniqueinClass($conn,$subject,$subjectname,$classId);
                         if ($uniqueSubject == "true")
                         { 
                            $report_holder = add_subject_to_class_id($conn,$subject,$subjectname,$classID);
                        }else{
                            $report_holder[] = "subject name already exist in " .$class_in_subclass. " class";
                        }
                        
                    }
                    $response = json_encode($report_holder);
                    echo json_encode(array('status' => true,'message' =>$response));
                }else{
                    echo json_encode(array('status' => false,'message' =>"There are no class in specified section."));      
                }
            } else {
                http_response_code(405);
                echo json_encode(array('status' => false,'message' => "Incomplete parameters. Please provide a class name or section name."));

            }
        } else {
            http_response_code(405);
            echo json_encode(array('status' => false,'message' => "Required parameters not provided."));
        }

        // Close the database connection
        $conn->close();

    }else {
        // Invalid HTTP method
        http_response_code(405);
        echo json_encode(array('status' => false,'message' => 'Invalid HTTP method'));
    }
}else{
    http_response_code(405);
    echo json_encode(array('status' => false,'message' =>'Invalid school id'));
}
?>
