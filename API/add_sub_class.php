<?php

/*
This PHP script appears to handle an API call to add a subclass to a school's class structure. Here's a breakdown of the script's main components and functionality:

1. **Session Start**: The script starts by initializing the session.

2. **Include Files**: It includes two external PHP files, 'connect.php' (presumably for database connection) and 'common_functions.php' (presumably for common utility functions).

3. **School ID**: The script sets the school ID to 9 in the user's session.

4. **API Request Handling**:
   - It checks if the request method is POST.
   - It checks if the required parameters (class_name, section, subclass_name, and mode) are provided in the POST request.

5. **Parameter Sanitization**: The script sanitizes input data using `mysqli_real_escape_string` to prevent SQL injection.

6. **School Name Retrieval**: It retrieves the school name based on the school ID and converts spaces in the name to underscores. It then constructs table names for classes, subclasses, and sections using the school name.

7. **Class ID Retrieval**: It retrieves the class ID based on the provided class name.

8. **Add Subclass Logic**:
   - If the mode is "class" and the class ID exists, it checks if the provided subclass name is unique in the given class. If unique, it attempts to add the subclass to the class and returns a success or error message in JSON format.
   - If the mode is "section," it retrieves a list of class names in the specified section and attempts to add the subclass to each class, returning a response containing success or error messages for each attempt.

9. **JSON Response**:
   - The script sends JSON responses with a "status" (true or false) and a "message" containing relevant information, such as success messages or error messages.

10. **Error Handling**:
    - If there are missing parameters or an invalid request method (not POST), appropriate error messages are returned.

This script essentially acts as an API endpoint for adding subclasses to classes within a school, providing feedback about the success or failure of these operations. It also includes some basic error handling to report issues with the API request or database operations.

*/
session_start();
// Include your database connection
include 'connect.php';
include 'common_funtions.php';

$school_id = $_SESSION['school_id'] = 9;


// Check for the API call and parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the required parameters are provided
    if (isset($_POST['class_name']) && isset($_POST['section']) && isset($_POST['subclass_name']) && isset($_POST['mode'])) {
        $className = $_POST['class_name'];
        $sectionName = $_POST['section'];
        $subclass_name = $_POST['subclass_name'];
        $mode = $_POST['mode'];

        // Call the function to add the subclass
            // Sanitize input to prevent SQL injection
        $className = mysqli_real_escape_string($conn, $className);
        $sectionName = mysqli_real_escape_string($conn, $sectionName);
        $subclass_name = mysqli_real_escape_string($conn, $subclass_name);
        $mode = mysqli_real_escape_string($conn, $mode);

        $schoolName = getSchoolNameFromSchoolId($school_id, $conn);
        if ($schoolName  !== "" && $schoolName !== null )
        {
            $schoolName = str_replace(" ","_",$schoolName);
            $class  =  $schoolName."_class";
            $subclass =  $schoolName."_subclass";
            $section =  $schoolName."_section";
        
            // Get the classID based on the class name
            $classID = getClassIDFromClassname($className, $conn, $class);
        
            if ($mode === "class" && $classID !== null) {
                $unique_subclass =  isSubClassNameUnique($conn,$subclass,$subclass_name,$classID);
                if ($unique_subclass === "true"){
                    //Insert the new subclass into the 'subclass' table
                    $add_query = add_sub_class_to_class($conn,$subclass,$subclass_name,$classID);
                    if ($add_query == "true") {
                        echo json_encode(array('status' => true,'message' => "Subclass added successfully."));
                    } else {
                        echo json_encode(array('status' => false,'message' =>"Error adding subclass: " . mysqli_error($conn)));
                    }
                }else{
                        echo json_encode(array('status' => false,'message' =>"subclass name already exist in" .$class_in_subclass. "class"));
                }
            } 
        else if ($mode === "section"){
            $classInSection  = array();
            $classInSection = getAllClassNamesInSection($conn,$sectionName,$section,$class);
            //print_r($classInSection);
            $countClass = count($classInSection);
            if ($countClass > 0){
                $report_holder =  array();
                foreach ($classInSection as $class_in_subclass){
                    // Get the classID based on the class name
                     $classID = getClassIDFromClassname($class_in_subclass, $conn, $class);
                    // IS The subclass name unique
                     $unique_subclass =  isSubClassNameUnique($conn,$subclass,$subclass_name,$classID);
                    if ($unique_subclass === "true"){
                        $report_holder = add_sub_class_to_class($conn,$subclass,$subclass_name,$classID);
                    }else{
                        $report_holder[] = "subclass name already exist in " .$class_in_subclass. " class";
                    }
                    
                }
                $response = json_encode($report_holder);
                echo json_encode(array('status' => true,'message' =>$response));
            }else{
                echo json_encode(array('status' => false,'message' =>"There are no class in specified section."));      
            }
        }else{
            echo json_encode(array('status' => false,'message' =>"Invalid query mode."));
        }

    } 
    else{
        echo json_encode(array('status' => false,'message' => 'School name not found'));
    }
}else {
        echo json_encode(array('status' => false,'message' => 'Missing parameters'));
    }
} else {
    echo json_encode(array('status' => false,'message' => 'Invalid request method'));
}
?>
