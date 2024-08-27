<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is an admin
if ($_SESSION['UserType'] != 'Administrator') {
    header("Location: login.php");
    exit();
}

echo "Welcome, " . $_SESSION['Name'] . " " . ($_SESSION['Surname'] ?? '') . " (Administrator)";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <ul>
        <li><a href="add_bike.php">Add Bike</a></li>
        <li><a href="list_bikes.php">List Bikes</a></li>
        <li><a href="list_users.php">List Users</a></li>
        <li><a href="search_bike.php">Search Bike</a></li>
        <li><a href="search_user.php">Search User</a></li>
        <li><a href="rent_bike.php">Rent Bike for User</a></li>
        <li><a href="return_bike.php">Return Bike for User</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
