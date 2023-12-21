<?php

$custEmail = isset($_POST['custEmail']) ? $_POST['custEmail'] : '';
$custPassword = isset($_POST['custPassword']) ? $_POST['custPassword'] : '';

include 'databaseConnect.php';

// Validate and sanitize inputs
$custEmail = $conn->real_escape_string($custEmail);
$custPassword = $conn->real_escape_string($custPassword);

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Use prepared statement to prevent SQL injection
$crctPassQuery = $conn->prepare("SELECT accountpassword, fname FROM persons WHERE accesslevel = 'customer' AND email = ?");
$crctPassQuery->bind_param("s", $custEmail);
$crctPassQuery->execute();
$crctPassResult = $crctPassQuery->get_result();
$crctPassData = $crctPassResult->fetch_array();

$conn->close();

if ($crctPassData && password_verify($custPassword, $crctPassData['accountpassword'])) {
    // Password is correct
    ?>

    <script>
        localStorage.setItem('loggedCustomer','<?php echo htmlspecialchars($crctPassData['fname']); ?>');
        localStorage.setItem('loggedCustomerEmail','<?php echo htmlspecialchars($custEmail); ?>');
        location.replace("../customerProductView.php");
    </script>

    <?php
} else {
    // Password is incorrect
    header("Location: wrongLoginCustomer.html");
    exit();
}
?>
