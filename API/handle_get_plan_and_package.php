<?php
// Include the database connection file
include "connect.php";
include "common_funtions.php";

// Check if the request is a GET request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Query to fetch all pricing plans and their associated packages
    $getPlansAndPackagesSql = "SELECT pricing_plans.plan_name, pricing_plans.plan_description, GROUP_CONCAT(packages.package_name) AS package_names
        FROM pricing_plans
        LEFT JOIN plan_packages ON pricing_plans.plan_id = plan_packages.plan_id
        LEFT JOIN packages ON plan_packages.package_id = packages.package_id
        GROUP BY pricing_plans.plan_name";

    $result = $conn->query($getPlansAndPackagesSql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(array("message" => "Error in SQL query: " . $conn->error));
    } else {
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                "plan_name" => $row["plan_name"],
                "plan_description" => $row["plan_description"],
                "package_names" => explode(',', $row["package_names"])
            );
        }

        http_response_code(200);
        echo json_encode(array("status" => true,"message" => $data));
    }
} else {
    http_response_code(405);
    echo json_encode(array("status" => false,"message" => "Method Not Allowed"));
}
?>
