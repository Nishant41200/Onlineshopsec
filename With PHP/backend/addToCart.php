<?php
$productId = $_POST['productId'];
$productQty = $_POST['productQty'];
$personEmail = $_POST['personEmail'];
$productStock = $_POST['productStock'];

include 'databaseConnect.php';

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Validate and sanitize inputs
$productId = intval($productId);
$productQty = intval($productQty);
$personEmail = $conn->real_escape_string($personEmail);
$productStock = intval($productStock);

// Use prepared statements to prevent SQL injection
$updateStockQuery = $conn->prepare("UPDATE product SET productStock = productStock - ? WHERE productId = ?");
$updateStockQuery->bind_param("ii", $productQty, $productId);
$updateStockQuery->execute();

$costQuery = $conn->prepare("SELECT productprice FROM product WHERE productid = ?");
$costQuery->bind_param("i", $productId);
$costQuery->execute();
$oneCostResult = $costQuery->get_result();
$oneCost = $oneCostResult->fetch_array();

$personIdQuery = $conn->prepare("SELECT personid FROM persons WHERE email = ?");
$personIdQuery->bind_param("s", $personEmail);
$personIdQuery->execute();
$personIdResult = $personIdQuery->get_result();
$personId = $personIdResult->fetch_array();

$costPrice = $oneCost[0] * $productQty;

$tempResultQuery = $conn->prepare("SELECT * FROM cart WHERE personid = ? AND productid = ? AND deliveryid = 0");
$tempResultQuery->bind_param("ii", $personId[0], $productId);
$tempResultQuery->execute();
$tempResult = $tempResultQuery->get_result()->fetch_array();

sleep(1);

if (is_array($tempResult)) {
    $alreadyQtyQuery = $conn->prepare("SELECT quantity, cost FROM cart WHERE personid = ? AND productid = ? AND deliveryid = 0");
    $alreadyQtyQuery->bind_param("ii", $personId[0], $productId);
    $alreadyQtyQuery->execute();
    $alreadyQtyResult = $alreadyQtyQuery->get_result()->fetch_array();
    sleep(1);

    $updateCartQuery = $conn->prepare("UPDATE cart SET quantity = quantity + ?, cost = cost + ? WHERE personid = ? AND productid = ? AND deliveryid = 0");
    $updateCartQuery->bind_param("iiii", $productQty, $costPrice, $personId[0], $productId);
    $updateCartQuery->execute();
} else {
    $insertCartQuery = $conn->prepare("INSERT INTO cart VALUES (?, ?, ?, ?, NULL)");
    $insertCartQuery->bind_param("iiii", $personId[0], $productId, $productQty, $costPrice);
    $insertCartQuery->execute();
}

sleep(1);

header("Location: ../customerProductView.php");
exit();

// Close prepared statements and connection
$updateStockQuery->close();
$costQuery->close();
$personIdQuery->close();
$tempResultQuery->close();

if (isset($alreadyQtyQuery)) {
    $alreadyQtyQuery->close();
}

if (isset($updateCartQuery)) {
    $updateCartQuery->close();
}

if (isset($insertCartQuery)) {
    $insertCartQuery->close();
}

$conn->close();
?>
