<?php
include 'databaseConnect.php';

// Validate and sanitize input
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

// Use prepared statement to prevent SQL injection
$personIDQuery = $conn->prepare("SELECT personid FROM persons WHERE email = ?");
$personIDQuery->bind_param("s", $email);
$personIDQuery->execute();
$personIDResult = $personIDQuery->get_result();
$personID = $personIDResult->fetch_array();

// Use prepared statement to prevent SQL injection
$costQuery = $conn->prepare("SELECT SUM(cost) FROM cart WHERE deliveryid = 0 AND personid = ?");
$costQuery->bind_param("i", $personID[0]);
$costQuery->execute();
$costResult = $costQuery->get_result();
$cost = $costResult->fetch_array();

// Use prepared statement to prevent SQL injection
$insertDeliveryQuery = $conn->prepare("INSERT INTO delivery (personID, totalCost) VALUES (?, ?)");
$insertDeliveryQuery->bind_param("id", $personID[0], $cost[0]);
$insertDeliveryQuery->execute();

// Use prepared statement to prevent SQL injection
$updateCartQuery = $conn->prepare("UPDATE cart SET deliveryID = (SELECT MAX(deliveryID) FROM delivery) WHERE personID = ? AND deliveryID = 0");
$updateCartQuery->bind_param("i", $personID[0]);
$updateCartQuery->execute();

sleep(1);

require "orderPlaced.html";

// Close prepared statements and connection
$personIDQuery->close();
$costQuery->close();
$insertDeliveryQuery->close();
$updateCartQuery->close();
$conn->close();
?>