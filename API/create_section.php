<?php
    /*
This PHP script appears to be responsible for inserting a new section name into a database. Here's a breakdown of what the script does:

    1. **Session Start**: It starts a PHP session and sets a session variable `school_id` to 9.
    
    2. **Include Files**: The script includes two external PHP files, `connect.php` and `common_functions.php`. These files likely contain functions and database connection details needed for the script's operation.
    
    3. **Section Name Retrieval**: It retrieves the section name from a POST request, which should have been sent by a client-side form or another script.
    
    4. **Validation**: It checks whether the `sectionName` variable is empty. If it is empty, the script returns a JSON response with a "400 Bad Request" status and an error message indicating that the section name is required.
    
    5. **School Name Retrieval**: It calls a function `getSchoolNameFromSchoolId` with the `school_id` and the database connection (`$conn`) as parameters to get the school name. The school name is then modified to replace spaces with underscores.
    
    6. **Table Name Formation**: It constructs a table name (`$section`) based on the modified school name. This implies that different schools might have separate tables for sections, and the table name is dynamic.
    
    7. **Database Insertion**: It constructs an SQL query to insert the section name into the dynamically determined table. If the insertion is successful, it returns a JSON response with a "200 OK" status and a success message. If the insertion fails, it returns a "500 Internal Server Error" status with an error message indicating the failure and the MySQL error message.
    
    8. **School Name Check**: If there was an issue obtaining the school name or if it's empty, it returns an error response with a "500 Internal Server Error" status, indicating the failure to get the school name.
    
    9. **Database Connection Closure**: The script closes the database connection using `mysqli_close($conn)`.
    
    In summary, this script is designed to insert a section name into a dynamically determined database table based on the school name. It performs basic validation, handles potential errors, and returns JSON responses to indicate the status of the insertion process.


    */
session_start();
$school_id = $_SESSION['school_id'] = 9;
// Include the database connection
include 'connect.php';
include 'common_funtions.php';

// Get the section name from the request
 $sectionName = $_POST['sectionName']; 

// Check if the section name is provided
if (empty($sectionName)) {
    // Return an error response if the section name is missing
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Section name is required"]);
    exit;
}

 $schoolName = getSchoolNameFromSchoolId($school_id, $conn);  
if ($schoolName  !== null && $schoolName !== "" )
{
    $schoolName = str_replace(" ","_",$schoolName);
     $section  =  $schoolName."_section";
    // Insert the section name into the section table
    $query = "INSERT INTO $section (section_name) VALUES ('$sectionName')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => true,'message' =>  "Section name inserted successfully"]);
    } else {
        // Return an error response if the insertion fails
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => false,'message' => "Failed to insert section name: " . mysqli_error($conn)]);
    }
}else{
    // Return an error response if the insertion fails
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => false,'message' => "Failed to get school name"]);
}

// Close the database connection
mysqli_close($conn);
?>
