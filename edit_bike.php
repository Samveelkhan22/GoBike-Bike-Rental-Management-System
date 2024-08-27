<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is an admin
if ($_SESSION['UserType'] != 'Administrator') {
    header("Location: login.php");
    exit();
}

// Debugging output: Check URL parameters and session variables
// echo "<pre>";
// print_r($_GET);
// echo "</pre>";

// Check if 'id' parameter is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: Bike ID is not provided.";
    exit();
}

// Get the bike ID from the URL
$bikeID = intval($_GET['id']); // Ensure the ID is an integer

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $rentingLocation = $_POST['renting_location'];
    $description = $_POST['description'];
    $costPerHour = $_POST['cost_per_hour'];
    $status = $_POST['status'];

    // Input validation
    if (empty($rentingLocation) || empty($costPerHour)) {
        echo "Renting location and cost per hour are required.";
    } else {
        // Prepare an SQL statement to update the bike data
        $stmt = $conn->prepare("UPDATE Bikes SET RentingLocation = ?, Description = ?, CostPerHour = ?, Status = ? WHERE BikeID = ?");
        $stmt->bind_param("ssdsi", $rentingLocation, $description, $costPerHour, $status, $bikeID);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "Bike updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
} else {
    // Fetch the current bike details
    $stmt = $conn->prepare("SELECT RentingLocation, Description, CostPerHour, Status FROM Bikes WHERE BikeID = ?");
    $stmt->bind_param("i", $bikeID);
    $stmt->execute();
    $stmt->bind_result($rentingLocation, $description, $costPerHour, $status);
    if ($stmt->fetch()) {
        // Data fetched successfully
    } else {
        echo "Error: Bike not found.";
        exit();
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Bike</title>
</head>
<body>
    <h2>Edit Bike</h2>
    <form action="edit_bike.php?id=<?php echo htmlspecialchars($bikeID); ?>" method="post">
        <label for="renting_location">Renting Location:</label><br>
        <input type="text" id="renting_location" name="renting_location" value="<?php echo htmlspecialchars($rentingLocation); ?>" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea><br><br>
        <label for="cost_per_hour">Cost Per Hour:</label><br>
        <input type="number" step="0.01" id="cost_per_hour" name="cost_per_hour" value="<?php echo htmlspecialchars($costPerHour); ?>" required><br><br>
        <label for="status">Status:</label><br>
        <select id="status" name="status">
            <option value="Available" <?php if ($status == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Rented" <?php if ($status == 'Rented') echo 'selected'; ?>>Rented</option>
        </select><br><br>
        <input type="submit" value="Update Bike">
    </form>
</body>
</html>
