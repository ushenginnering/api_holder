<?php

include "connect.php";
require "common_funtions.php";

$school_array =  array();
$school_array = get_all_schools($conn);

if (count($school_array) > 0 ) {
    echo json_encode(array('status' => true,'message' =>$school_array ));  
 } else{
    echo json_encode(array('status'=> false,'message'=> 'no schools found'));
 }
?>
