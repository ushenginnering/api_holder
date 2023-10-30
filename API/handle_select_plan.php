<?php
// Include the database connection file
include 'db_connection.php';

// Check if the request is a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve the plan name from the query string
    $selectedPlanName = $_GET['plan_name'];

    // Validate the plan name
    if (empty($selectedPlanName)) {
        http_response_code(400);
        echo json_encode(array("message" => "Plan name is required."));
    } else {
        // Query to fetch plan information and associated packages
        $getPlanInfoSql = "SELECT pricing_plans.plan_name, pricing_plans.plan_description, packages.package_name, packages.package_price
            FROM pricing_plans
            JOIN plan_packages ON pricing_plans.plan_id = plan_packages.plan_id
            JOIN packages ON plan_packages.package_id = packages.package_id
            WHERE pricing_plans.plan_name = '$selectedPlanName'";

        $result = $conn->query($getPlanInfoSql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(array("message" => "Error in SQL query: " . $conn->error));
        } else {
            // Fetch and format the results
            $planData = array("plan_name" => $selectedPlanName, "packages" => array());
            while ($row = $result->fetch_assoc()) {
                $planData["packages"][] = array(
                    "package_name" => $row["package_name"],
                    "package_price" => $row["package_price"]
                );
            }

            http_response_code(200);
            echo json_encode($planData);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
