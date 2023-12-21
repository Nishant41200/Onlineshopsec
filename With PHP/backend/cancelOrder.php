<?php
include 'databaseConnect.php';

// create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
if (mysqli_connect_error()) {
    die('Connection Error('. mysqli_connect_errno().')'. mysqli_connect_error());
}

// Sanitize and validate inputs
$delID = isset($_POST['cancelOrderID']) ? intval($_POST['cancelOrderID']) : 0;
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$delStatus = "Order Canceled";
$delStatus2 = "Requested Cancellation";

// Use prepared statement to prevent SQL injection
$statusQuery = $conn->prepare("SELECT deliveryStatus FROM delivery WHERE deliveryID = ?");
$statusQuery->bind_param("i", $delID);
$statusQuery->execute();
$statusResult = $statusQuery->get_result();
$status = $statusResult->fetch_array();

if ($status[0] == $delStatus) {
    require "orderCancelFailed.html";
} else {
    $returnToStockQuery = $conn->prepare("SELECT product.productID, cart.quantity FROM product, cart WHERE cart.productID = product.productID AND cart.deliveryID = ?");
    $returnToStockQuery->bind_param("i", $delID);
    $returnToStockQuery->execute();
    $returnToStockResult = $returnToStockQuery->get_result();

    while ($row = $returnToStockResult->fetch_array()) {
        $prodid = $row[0];
        $prodqty = $row[1];
        $updateStockQuery = $conn->prepare("UPDATE product SET productStock = productStock + ? WHERE productID = ?");
        $updateStockQuery->bind_param("ii", $prodqty, $prodid);
        $updateStockQuery->execute();
    }

    $updateDeliveryStatusQuery = $conn->prepare("UPDATE delivery SET deliveryStatus = ? WHERE deliveryID = ?");
    $updateDeliveryStatusQuery->bind_param("si", $delStatus2, $delID);
    $updateDeliveryStatusQuery->execute();

    sleep(1);

    require "orderCancelSuccess.html";
}

// Close prepared statements and connection
$statusQuery->close();
$returnToStockQuery->close();
$updateStockQuery->close();
$updateDeliveryStatusQuery->close();
$conn->close();
?>
