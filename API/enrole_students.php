<?php

/*
This PHP script appears to handle the enrollment of students in a school database. Let's break down its functionality:

1. **Session Start**: It starts a PHP session and sets a session variable `$school_id` to the value 9. This is presumably used to identify the school for which students are being enrolled.

2. **Include Dependencies**: The script includes two external files, 'connect.php' and 'common_functions.php', which likely contain database connection details and various helper functions used within the script.

3. **Content Type**: It sets the response content type to JSON, indicating that the script will output JSON data.

4. **Request Method Check**: The script checks if the HTTP request method is POST. It expects client applications to send data via POST requests.

5. **Data Processing**:
   - It retrieves various parameters from the POST request, including `className`, `subjectName`, `sectionName`, and `mode`.
   - It uses the school ID to derive table names based on the school's name, such as `$schoolName_class`, `$schoolName_subclass`, etc.
   - It retrieves additional student data from the POST request, such as `studentId`, `firstname`, `lastname`, and more.
   - It generates the current session and term, as well as a password (using MD5) for the student.
   - It handles several optional parameters like `othername`, `soo`, `lg`, `dob`, `sporthouse`, and more.

6. **Database Insertion**: It constructs an SQL query to insert the student data into the database. This query includes the derived table names based on the school name.

7. **Database Insertion Check**: The script attempts to execute the SQL query. If the insertion is successful, it responds with a JSON object indicating success. If there's an error, it responds with a 500 Internal Server Error status code and an error message.

8. **Error Handling**:
   - If the school name is not found, it responds with a 405 status code and a "School not found" message.
   - If the HTTP request method is not POST, it responds with a 405 status code and an "Invalid HTTP method" message.

In summary, this script is designed to handle student enrollment for a specific school. It takes various student-related parameters, inserts them into a school-specific database, and responds with JSON data indicating success or failure. It also includes some basic error handling for cases where the school is not found or the HTTP method is not POST.
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
        $subclass =  $schoolName."_subclass";
        $section =  $schoolName."_section";
        $students = $schoolName."_students";


        // Get parameters from the request
        $studentId = $_POST['studentId'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $admissionNumber = $_POST['admissionNumber'];
        $sex = $_POST['sex'];
        $curClass = $_POST['curClass'];

        // Generate current session and term (you can customize this as needed)
        $currentSession = get_school_session($conn,$school_id);
        $currentTerm = get_school_term($conn,$school_id);

        // Generate a password for the student (you may want to implement a more secure method)
        $password = md5($studentId); // Hash the student ID as the initial password

        // Additional optional parameters
        $othername = $_POST['othername'] ?? '';
        $soo = $_POST['soo'] ?? '';
        $lg = $_POST['lg'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $sporthouse = $_POST['sporthouse'] ?? '';
        $school = $_POST['school'] ?? '';
        $picture = $_POST['picture'] ?? '';
        $subClass = $_POST['subClass'] ?? '';
        $move = $_POST['move'] ?? 0;
        $email = $_POST['email'] ?? '';
        $session = $_POST['session'] ?? '';
        $session = empty($session) ? $currentSession : $session; // Use current session if not provided
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $guardiansName = $_POST['guardiansName'] ?? '';

        // Insert the student data into the database
        $query = "INSERT INTO $students (studentId, firstname, lastname, othername, soo, lg, dob, sex, class, sporthouse, adminnumber, school, session, cur_class, password, move, email, 2023_2024_class, 2023_2024_subclass, phone, address, guardians_name)
                VALUES ('$studentId', '$firstname', '$lastname', '$othername', '$soo', '$lg', '$dob', '$sex', '$curClass', '$sporthouse', '$admissionNumber', '$school', '$session', '$curClass', '$password', '$move', '$email', '', '', '$phone', '$address', '$guardiansName')";

        if (mysqli_query($conn, $query)) {
            $response = [
                "status"  => true,
                "message" => "Student enrolled successfully.",
            ];
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode([ "status"  => false, "message" => "Failed to enroll student: " . mysqli_error($conn)]);
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
?>
