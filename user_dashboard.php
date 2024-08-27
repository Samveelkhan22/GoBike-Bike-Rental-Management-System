<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is logged in as a regular user
if ($_SESSION['UserType'] != 'User') {
    header("Location: login.php");
    exit();
}

echo "Welcome, " . $_SESSION['Name'] . " " . ($_SESSION['Surname'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
</head>
<body>
    <h2>User Dashboard</h2>
    <ul>
        <li><a href="list_bikes.php">List Available Bikes</a></li>
        <li><a href="rent_bike.php">Rent a Bike</a></li>
        <li><a href="return_bike.php">Return a Bike</a></li>
        <li><a href="past_rentals.php">View Past Rentals</a></li>
        <li><a href="current_rentals.php">List Current Rentals</a></li>
        <li><a href="search_bike.php">Search Bike</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
