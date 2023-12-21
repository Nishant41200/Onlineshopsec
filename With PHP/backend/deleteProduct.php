<?php
    $productId = isset($_POST["productID"]) ? $_POST["productID"] : '';

    include 'databaseConnect.php';

    // Validate and sanitize input
    $productId = intval($productId);

    // Create connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

    // Use prepared statement to prevent SQL injection
    $deleteProductQuery = $conn->prepare("DELETE FROM product WHERE productid = ?");
    $deleteProductQuery->bind_param("i", $productId);
    $deleteProductQuery->execute();

    sleep(2);

    // Redirect after deletion
    header("Location: ../adminProductManage.php");
    exit();

    // Close prepared statement and connection
    $deleteProductQuery->close();
    $conn->close();
?>
