<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user data from session
$name = $_SESSION['Name'];
$userType = $_SESSION['UserType'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GoBike</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
    <p>Your User Type: <?php echo htmlspecialchars($userType); ?></p>

    <!-- Add links to other functionalities here -->
    <a href="rent_bike.php">Rent a Bike</a><br>
    <a href="view_rentals.php">View Your Rentals</a><br>
    <a href="logout.php">Logout</a>
</body>
</html>
