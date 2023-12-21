<?php

$adminEmail = $_POST['adminEmail'];
$adminPassword = $_POST['adminPassword'];

include 'databaseConnect.php';

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Use prepared statements to prevent SQL injection
$stmtPass = $conn->prepare("SELECT accountpassword FROM persons WHERE accesslevel='admin' AND email=?");
$stmtPass->bind_param("s", $adminEmail);
$stmtPass->execute();
$stmtPass->bind_result($crctPass);
$stmtPass->fetch();
$stmtPass->close();

$stmtName = $conn->prepare("SELECT fname FROM persons WHERE email=?");
$stmtName->bind_param("s", $adminEmail);
$stmtName->execute();
$stmtName->bind_result($adminName);
$stmtName->fetch();
$stmtName->close();

$conn->close();

if ($crctPass == $adminPassword) { ?>

    <script>
        localStorage.setItem('loggedAdmin', '<?php echo $adminName; ?>');
        localStorage.setItem('loggedAdminEmail', '<?php echo $adminEmail; ?>');
        location.replace("../adminProductManage.php");
    </script>

<?php
} else {
    header("Location: wrongLoginAdmin.html");
    exit();
}
?>
