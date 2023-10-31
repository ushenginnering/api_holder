<?php

 
// create database tables
function create_tables($conn, $school_name){
    $school_name = str_replace(" ","_",$school_name);
    $tableStructures = array(
        'school' => '(
            school_Id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            address VARCHAR(255),
            phone VARCHAR(15),
            email VARCHAR(255),
            session VARCHAR(50),
            term VARCHAR(50),
            logo VARCHAR(255),
            moto TEXT,
            influencerId TEXT,
            schoolPassword VARCHAR(255)
        )',
        'users' => '(
            userid INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255),
            username VARCHAR(50),
            password VARCHAR(255),
            phone VARCHAR(15),
            schoolID INT,
            status VARCHAR(50),
            signature VARCHAR(255),
            verified_email VARCHAR(255),
            verification_code VARCHAR(255),
            new_signup_date DATETIME DEFAULT CURRENT_TIMESTAMP, 
            last_seen_date DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'contact_requests' => '(
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            school_name VARCHAR(100),
            capacity_ranges VARCHAR(255),
            referral_source VARCHAR(100),
            desired_results TEXT, 
            email  VARCHAR(100),
            submission_date DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'plan_packages' =>  '(
            id INT AUTO_INCREMENT PRIMARY KEY,
            plan_id INT,
            package_id INT
        )',
        'packages' => '(
            package_id INT AUTO_INCREMENT PRIMARY KEY,
            package_name VARCHAR(50),
            package_price DECIMAL(10, 2)  -- Using DECIMAL for precise prices
        )',
        'pricing_plans' => '(
            plan_id INT AUTO_INCREMENT PRIMARY KEY,
            plan_name VARCHAR(50),
            plan_description VARCHAR(255)
        )',
        $school_name.'_class' => '(
            classID INT AUTO_INCREMENT PRIMARY KEY,
            schoolID INT,
            class_name VARCHAR(50),
            classorder INT,
            formTeacher INT,
            promote BOOLEAN,
            class_section_id INT,
            graduating_class BOOLEAN,
            mok_test_number INT,
            mok_test_status BOOLEAN,
            result_processing_engine VARCHAR(255),
            max_test_score DECIMAL(10, 2),
            max_exam_score DECIMAL(10, 2),
            forward BOOLEAN,
            max_first_test_score DECIMAL(10, 2),
            max_snd_test_score DECIMAL(10, 2),
            affective_array TEXT
        )',
        
        $school_name.'_subclass' => '(
            subclassID INT AUTO_INCREMENT PRIMARY KEY,
            classID INT,
            subclass_name VARCHAR(50)
        )',

        $school_name.'_subject' => '(
            subjectId INT AUTO_INCREMENT PRIMARY KEY,
            classID VARCHAR(50),
            subjectName VARCHAR(100),
            teacherId INT,
            groupname VARCHAR(50)
        )',

        $school_name.'_students' => '(
            id INT AUTO_INCREMENT PRIMARY KEY,
            studentId VARCHAR(50),
            firstname VARCHAR(50),
            lastname VARCHAR(50),
            othername VARCHAR(50),
            soo VARCHAR(50),
            lg VARCHAR(50),
            dob DATE,
            sex VARCHAR(10),
            class VARCHAR(50),
            sporthouse VARCHAR(50),
            adminnumber VARCHAR(50),
            school VARCHAR(255),
            session VARCHAR(50),
            cur_class VARCHAR(50),
            picture BLOB,
            subclass VARCHAR(50),
            password VARCHAR(255),
            move BOOLEAN,
            email VARCHAR(255),
            2023_2024_class VARCHAR(50),
            2023_2024_subclass VARCHAR(50),
            phone VARCHAR(15),
            address VARCHAR(255),
            guardians_name VARCHAR(100)
        )',

        $school_name.'_resulttarget' => '(
            resultId INT AUTO_INCREMENT PRIMARY KEY,
            session INT,
            term INT,
            class INT,
            schoolID INT,
            studentID INT
        )',

        $school_name.'_score' => '(
            ScoreId INT AUTO_INCREMENT PRIMARY KEY,
            StudentId INT,
            ResultId INT,
            Subject VARCHAR(100),
            1st_Test_Score DECIMAL(10, 2),
            2nd_Test_Score DECIMAL(10, 2),
            CA_Score DECIMAL(10, 2),
            Exam_Score DECIMAL(10, 2)
        )',    
        $school_name.'affectivescore' => '(
            affectiveScoreId INT AUTO_INCREMENT PRIMARY KEY,
            studentID INT,
            resultID INT,
            traitID INT,
            trait_score DECIMAL(10, 2)
        )',
    
        $school_name.'affective' => '(
            traitID INT AUTO_INCREMENT PRIMARY KEY,
            traitName VARCHAR(100)
        )',
    
        $school_name.'attendance' => '(
            attendanceScoreId INT AUTO_INCREMENT PRIMARY KEY,
            studentID INT,
            resultID INT,
            times_present INT,
            times_absent INT
        )',
    
        $school_name.'comment' => '(
            commentScoreId INT AUTO_INCREMENT PRIMARY KEY,
            studentID INT,
            resultID INT,
            teacherComment TEXT,
            headTeacherComment TEXT
        )',
    
        $school_name.'automated_comment' => '(
            autocommentid INT AUTO_INCREMENT PRIMARY KEY,
            classID INT,
            teachersComment TEXT,
            headTeacherComment TEXT,
            averageApplicable DECIMAL(10, 2)
        )',
    
        $school_name.'processResult' => '(
            processId INT AUTO_INCREMENT PRIMARY KEY,
            resultId INT,
            average DECIMAL(10, 2),
            position INT,
            next_term_begins DATETIME,
            term_end DATETIME,
            this_term_begins DATETIME,
            process_status VARCHAR(50),
            distribute_result_status VARCHAR(50)
        )',
    );

    $results = [];

    foreach ($tableStructures as $tableName => $tableStructure) {
      $sql = "CREATE TABLE IF NOT EXISTS $tableName $tableStructure";

        if (mysqli_query($conn, $sql)) {
            $results[$tableName] = 'Table created successfully';
        } else {
            $results[$tableName] = 'Error creating table: ' . mysqli_error($conn);
        }
    }

    
    return $results;
}
// Find the term
function generate_academic_session() {
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $currentMonth = date('n'); // Get the current month as a number (1-12)

    // Determine the academic session based on the current month
    if ($currentMonth >= 9) {
        return $currentYear . '-' . $nextYear; // Academic session starts in September
    } else {
        return ($currentYear - 1) . '-' . $currentYear; // Academic session starts before September
    }
}
// find the accademic session
function generate_academic_term() {
    $currentMonth = date('n'); // Get the current month as a number (1-12)

    // Determine the academic term based on the current month
    if ($currentMonth >= 9 && $currentMonth <= 12) {
        return '1'; // First term (e.g., Fall)
    } elseif ($currentMonth >= 1 && $currentMonth <= 4) {
        return '2'; // Second term (e.g., Spring)
    } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
        return '3'; // Third term (e.g., Summer)
    } else {
        return ''; // Unknown term
    }
}
// get school id by school name
function get_this_school_id_by_name($conn, $school_name) {
    $sql = "SELECT school_Id FROM school WHERE name = '$school_name'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['school_Id'];
    } else {
        return null; // Return null if the school name is not found
    }
}

function get_all_schools($conn){
    $getSchoolNameSql = "SELECT name FROM school";
    $result = $conn->query($getSchoolNameSql);

    if (!$result) {
        $schools  =  null;
    }

    $schools = array();

    while ($row = $result->fetch_assoc()) {
        $schools[] = $row['name'];
    }

    $result->close();

    return $schools;
}

// find school name from school id
function getSchoolNameFromSchoolId($school_id, $conn){
    $getSchoolNameSql = "SELECT name FROM school WHERE school_id = '$school_id'";
    $result = $conn->query($getSchoolNameSql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return null;
    }
}
function get_school_term($conn,$school_id){
    $getSchoolNameSql = "SELECT term FROM school WHERE school_id = '$school_id'";
    $result = $conn->query($getSchoolNameSql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['term'];
    } else {
        return null;
    }
}
function get_school_session($conn,$school_id){
    $getSchoolNameSql = "SELECT session FROM school WHERE school_id = '$school_id'";
    $result = $conn->query($getSchoolNameSql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['session'];
    } else {
        return null;
    }
}
// add new user record
function save_user($conn, $school_admin_name, $email, $phone, $schoolID, $status, $signature, $school_password) {
    $usersTable = 'users';
   
     $sql = "INSERT INTO $usersTable (email, username, password, phone, schoolID, status, signature) 
            VALUES ('$email', '$school_admin_name', '$school_password', '$phone', '$schoolID', '$status', '$signature')";

    if (mysqli_query($conn, $sql)) {
        return "User data inserted successfully.<br>";
    } else {
        return "Error inserting user data: " . mysqli_error($conn) . "<br>";
    }
}
// add new school record
function create_school($conn, $school_name, $address, $phone, $email, $current_session, $term, $logo_location, $moto, $influencerId,$school_password){
    $sql = "INSERT INTO school (name, address, phone, email, session, term, logo, moto, influencerId, schoolPassword) 
            VALUES ('$school_name', '$address', '$phone', '$email', '$current_session', '$term', '$logo_location', '$moto', '$influencerId','$school_password')";

    if (mysqli_query($conn, $sql)) {
        return "School data inserted successfully.<br>";
    } else {
        return "Error inserting school data: " . mysqli_error($conn) . "<br>";
    }
}
// find section id based on section name
function getSchoolsectionIdFromSchoolsectonname($section_name, $conn, $section){
   
    $getSectionIdSql = "SELECT section_id FROM $section WHERE section_name = '$section_name'";
    
    $sectionId = null;
    
     $result = mysqli_query($conn,$getSectionIdSql);
    
    if ($result !== false && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
         $sectionId = $row['section_id'];
    }else {
        $sectionId =  "no response";
    }
    
    return $sectionId;
}
// check is class name is unique
function isClassNameUnique($conn, $className, $class){
    $getUniqueClass = "SELECT class_name FROM $class WHERE class_name = '$className'";
    $result = mysqli_query($conn,$getUniqueClass);
    
    if (mysqli_num_rows($result) > 0 ) {
        return "false";
    }else {
        return "true";
    }
    
}
// check of suibclass name is unique
function isSubClassNameUnique($conn,$subclass,$subclass_name,$classID){
    $getUniqueClass = "SELECT subclass_name FROM $subclass WHERE subclass_name = '$subclass_name' and classID = '$classID'";
    $result = mysqli_query($conn,$getUniqueClass);
    
    if (mysqli_num_rows($result) > 0 ) {
        return "false";
    }else {
        return "true";
    }
}
//check if subject name is unique in class
function issubjectUniqueinClass($conn,$subject,$subjectname,$classId){
    $getUniqueSubject = "SELECT subjectName FROM $subject WHERE subjectName = '$subjectname' and classID = '$classId'";
    $result = mysqli_query($conn,$getUniqueSubject);
    
    if (mysqli_num_rows($result) > 0 ) {
        return "false";
    }else {
        return "true";
    } 
}
// Function to get classID based on class name
function getClassIDFromClassname($className, $conn, $class){
    // Sanitize input to prevent SQL injection
    $className = mysqli_real_escape_string($conn, $className);

    // Query to get the classID based on class name
    $sql = "SELECT classID FROM $class WHERE class_name = '$className' LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['classID'];
    }

    return null;
}
// find class id's related to section
function getClassIDsBySectionName($conn, $sectionname){
    $query = "SELECT classID FROM class WHERE class_section_id IN (SELECT sectionID FROM section WHERE section_name = '$sectionname')";
    $result = mysqli_query($conn, $query);
    $classIDs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $classIDs[] = $row['classID'];
    }
    return $classIDs;
}
// Function to get all the class names in a section and return in arrary format
function getAllClassNamesInSection($conn,$sectionName,$section,$class){
    // Sanitize input to prevent SQL injection
    $sectionName = mysqli_real_escape_string($conn, $sectionName);
    // Get section id from the name provided
    $sectionId =  getSchoolsectionIdFromSchoolsectonname($sectionName, $conn, $section);
    //reteive classnames related to subclass
    $sql = "SELECT class_name FROM $class WHERE class_section_id = '$sectionId'";
    $class_name = array();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row1 = mysqli_fetch_array($result,MYSQLI_NUM)) { 
            $class_name[] =  $row1[0];
        }
    }
    return $class_name;      
}
//add subclass to a specified class by ID
function add_sub_class_to_class($conn,$subclass,$subclass_name,$classID){
    $sql = "INSERT INTO $subclass (classID, subclass_name) VALUES ('$classID', '$subclass_name')";
    
    if (mysqli_query($conn, $sql)) {
        return "true";
    } else { 
        return "false";
    }
}
// add subject to class based on class id
function add_subject_to_class_id($conn,$subject,$subjectname,$classId){
    $sql = "INSERT INTO $subject (classID, subjectName, teacherId, groupname) 
           VALUES ('$classId', '$subjectname', 0, '')";
   if (mysqli_query($conn, $sql)) {
       return "true";
   } else {
       return "false";
   }
}
// delete class with class namr
function delete_class_by_classID($conn, $class,$classID){
  $deleteClassQuery = "DELETE FROM $class WHERE classID = '$classID'";
  if (mysqli_query($conn,$deleteClassQuery)){
    return "true";
  }else{
    return "false";
  }
}
// delete subclass from class based on class id
function delete_sub_class_from_classID($conn,$subclass,$classID){
    // Class row deleted successfully, now delete associated subclass records
    $deleteSubclassQuery = "DELETE FROM $subclass WHERE classID =  '$classID' ";
    if (mysqli_query($conn,$deleteSubclassQuery)){
        return "true";
    }else{
        return "false";
    }

}
// delete subject from class based on class id
function deleteSubjectsByClassID($conn, $classID,$subjectname,$subject){
     $query = "DELETE FROM $subject WHERE classID = '$classID'";
    if ($subjectname !== ""){
        $query =  $query." and subjectName = '$subjectname'";
    }
    if (mysqli_query($conn, $query)){
        return "true";
    }else{
        return "false";
    }
}
function fetchsubjectsfromclassID($conn,$subject,$classID){
    // Query to show subject and class ID for the specified class name
    $query = "SELECT subjectName FROM $subject WHERE classID = '$classID'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return json_encode($data);
    } else {
        return "Error: " . mysqli_error($conn);
    }
}
function getstudentsdetailsfromclassID($conn,$students,$classID){
    $query = "SELECT * from $students where classID =  '$classID'";
        // Execute the query to retrieve student information
        $result = mysqli_query($conn, $query);

        // Check if there are any results
        if (mysqli_num_rows($result) > 0) {
            $students = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $students_array[] = $row;
            }
            return $students_array;
        } else {
            return null;
        }
}
function getstudentsdetailsfromadminnumber($conn,$students,$studentID){
    $query = "SELECT * from $students where adminnumber =  '$studentID'";
        // Execute the query to retrieve student information
        $result = mysqli_query($conn, $query);

        // Check if there are any results
        if (mysqli_num_rows($result) > 0) {
            $students = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $students_array[] = $row;
            }
            return $students_array;
        } else {
            return null;
        }
}
function UpdateClassOrder($conn,$ordered_class_array,$class){

    $response = array();
    // Iterate through the provided class names and update the class order
    foreach ($ordered_class_array as $position => $class_name) {
        $class_order = $position + 1; // Adding 1 to start the order from 1
        $updateStatement = $conn->prepare("UPDATE $class SET classorder = '$class_order' WHERE class_name = '$class_name'");
        if (mysqli_query($conn,$updateStatement)){
            $response[] = "True";
        }else{
            $response[] = "false";
        }
    }
        return $response;
}
function checkusersEmail($conn,$email,$password){
    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
    if ($password  !== ""){
        $checkEmailSql .= " and password  = '$password'";
    }
    $result = mysqli_query($conn,$checkEmailSql);  
    if (mysqli_num_rows($result) > 0) {
        return     $result ;
    }else{
        return false;
    }
}

function check_user_existence($conn, $email) {
    // SQL query to check if a user with the provided email exists
    $sql = "SELECT * FROM users WHERE email = ?";

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        return true; // User with this email exists
    } else {
        return false; // User with this email doesn't exist
    }
}

function updateOPTcode($conn,$code,$email){
    $updateVerificationCodeSql = "UPDATE users SET verification_code = '$code' WHERE email = '$email'";
    if (mysqli_query($conn,$updateVerificationCodeSql)) {
        return true;
    }else{
        return false;
    }
}


// Function to get stored OTP from the users table for a given email
function getStoredOTP($conn, $email) {
    $sql = "SELECT verification_code FROM users WHERE email = ?"; // Assuming 'otp' is the column name storing the OTP

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['otp']; // Return the stored OTP
    } else {
        return null; // If no OTP is found for the email
    }
}


function updatePassword($conn,$password,$email){
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $updateVerificationCodeSql = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
    if (mysqli_query($conn,$updateVerificationCodeSql)) {
        return true;
    }else{
        return false;
    }
}
   // Include the PHPMailer library
   require 'PHPMailer/src/Exception.php';
   require 'PHPMailer/src/PHPMailer.php';
   require 'PHPMailer/src/SMTP.php';

   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;
function sendPaymentConfirmationEmail($conn, $code,$email) {

    $body = "Hello,\n\n";
    $body .= "Click the link to confim your account: https://rapidsuite.ng/Pages/OTP.html?opt=$code&email=$email \n";
    $body .= "Keep all your password safe.\n\n";
    $body .= "Best Regards,\n";
    $body .= "rapidsuite.ng\n\n";

    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Disable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->IsHTML(false);
        $mail->Username = 'ushengineering@gmail.com'; // Your email address
        $mail->Password = 'usouuyhzmiyabjes'; // Your email password
        $mail->SetFrom('ushengineering@gmail.com');
        $mail->Subject = "Email Confirmation | rapidsuite.ng";
        $mail->Body = $body;
        $mail->AddAddress($email);

        $success = $mail->send();

        if ($success) {
           return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }


}
?>



