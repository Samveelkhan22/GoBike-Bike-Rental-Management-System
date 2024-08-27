<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Debugging output
if (!isset($_SESSION['UserType'])) {  // Use 'UserType' to match the variable in 'admin_dashboard.php'
    echo "Session variable 'UserType' is not set.";
} else {
    // echo "Session 'UserType': " . $_SESSION['UserType'];
}

// Check if the user is an admin
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] != 'Administrator') {
    echo "Redirecting to login...";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $rentingLocation = $_POST['renting_location'];
    $description = $_POST['description'];
    $costPerHour = $_POST['cost_per_hour'];

    // Input validation
    if (empty($rentingLocation) || empty($costPerHour)) {
        echo "Renting location and cost per hour are required.";
    } else {
        // Set the default status to 'Available'
        $status = 'Available';

        // Prepare an SQL statement to insert the bike data into the Bikes table
        $stmt = $conn->prepare("INSERT INTO Bikes (RentingLocation, Description, CostPerHour, Status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $rentingLocation, $description, $costPerHour, $status);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "Bike added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Bike</title>
</head>
<body>
    <h2>Add a New Bike</h2>
    <form action="add_bike.php" method="post">
        <label for="renting_location">Renting Location:</label><br>
        <input type="text" id="renting_location" name="renting_location" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description"></textarea><br><br>
        <label for="cost_per_hour">Cost Per Hour:</label><br>
        <input type="number" step="0.01" id="cost_per_hour" name="cost_per_hour" required><br><br>
        <input type="submit" value="Add Bike">
    </form>
</body>
</html>
