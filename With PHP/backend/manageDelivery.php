<?php
$deliveryid = isset($_POST['deliveryid']) ? $_POST['deliveryid'] : '';
$deliverydate = isset($_POST['deliverydate']) ? $_POST['deliverydate'] : '';
$deliverystatus = isset($_POST['deliverystatus']) ? $_POST['deliverystatus'] : '';

include 'databaseConnect.php';

// Validate and sanitize inputs
$deliveryid = intval($deliveryid);
$deliverydate = $conn->real_escape_string($deliverydate);
$deliverystatus = $conn->real_escape_string($deliverystatus);

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Use prepared statement to prevent SQL injection
$SELECT_ONE = "SELECT deliveryStatus FROM delivery WHERE deliveryid = ? LIMIT 1";
$stmt = $conn->prepare($SELECT_ONE);
$stmt->bind_param("i", $deliveryid);
$stmt->execute();
$stmt->bind_result($TEMP_DELIVERY_STATUS);
$stmt->store_result();
$stmt->fetch();
$rnum = $stmt->num_rows;
$stmt->close();

if ($rnum == 0) {
    require "failedDeliveryModify.html";
} else {
    // Use prepared statement to prevent SQL injection
    $updateDeliveryQuery = $conn->prepare("UPDATE delivery SET deliveryStatus = ?, deliveryDate = ? WHERE deliveryid = ?");
    $updateDeliveryQuery->bind_param("ssi", $deliverystatus, $deliverydate, $deliveryid);
    $updateDeliveryQuery->execute();
    $updateDeliveryQuery->close();

    sleep(1);
    require "successDeliveryModify.html";
}

$conn->close();
?>
