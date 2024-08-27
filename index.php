<?php
// Start session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['UserID'])) {
    if ($_SESSION['UserType'] == 'Administrator') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBike - Welcome</title>
</head>
<body>
    <h1>Welcome to GoBike</h1>
    <a href="login.php">Login</a> | 
    <a href="register.php">Register</a>
</body>
</html>
