<?php
include 'databaseConnect.php';

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
if (mysqli_connect_error()) {
    die('Connection Error('. mysqli_connect_errno().')'. mysqli_connect_error());
}

// Sanitize and validate the input
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT personID FROM persons WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($personID);
$stmt->fetch();
$stmt->close();

// Check if personID is valid
if ($personID) {
    // Use prepared statement for SELECT query
    $returnTOStock = $conn->prepare("SELECT productID, quantity FROM cart WHERE personID=? AND deliveryID=0");
    $returnTOStock->bind_param("i", $personID);
    $returnTOStock->execute();
    $returnTOStockResult = $returnTOStock->get_result();

    // Use prepared statement for UPDATE query
    $stmtUpdate = $conn->prepare("UPDATE product SET productStock = productStock + ? WHERE productID = ?");

    while ($row = mysqli_fetch_array($returnTOStockResult)) {
        $prodid = $row[0];
        $prodqty = $row[1];

        // Validate that $prodid and $prodqty are integers
        if (filter_var($prodid, FILTER_VALIDATE_INT) && filter_var($prodqty, FILTER_VALIDATE_INT)) {
            $stmtUpdate->bind_param("ii", $prodqty, $prodid);
            $stmtUpdate->execute();
        } else {
            // Handle the case where $prodid or $prodqty are not valid integers
            echo "Invalid productID or quantity";
        }
    }

    $stmtUpdate->close();
    $returnTOStock->close();

    // Use prepared statement for DELETE query
    $stmtDelete = $conn->prepare("DELETE FROM cart WHERE personID=? AND deliveryID=0");
    $stmtDelete->bind_param("i", $personID);

    // Validate that $personID is an integer
    if (filter_var($personID, FILTER_VALIDATE_INT)) {
        $stmtDelete->execute();
        $stmtDelete->close();

        sleep(1);

        require "cartClearSuccess.html";
    } else {
        // Handle the case where $personID is not a valid integer
        echo "Invalid personID";
    }
} else {
    // Handle the case where the personID is not valid (e.g., email not found)
    echo "Invalid email";
}

$conn->close();
?>
