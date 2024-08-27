<?php
session_start();
require 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

// Get the BikeID from the form submission
$BikeID = $_POST['BikeID'];
$UserID = $_SESSION['UserID'];
$StartDateTime = date('Y-m-d H:i:s');

// Insert the rental record
$stmt = $conn->prepare("INSERT INTO Rentals (UserID, BikeID, StartDateTime) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $UserID, $BikeID, $StartDateTime);

if ($stmt->execute()) {
    // Update bike status to "Rented"
    $stmt = $conn->prepare("UPDATE Bikes SET Status = 'Rented' WHERE BikeID = ?");
    $stmt->bind_param("i", $BikeID);
    $stmt->execute();

    echo "Bike rented successfully!<br>";
    echo "Start Time: " . $StartDateTime . "<br>";
    echo "<a href='user_dashboard.php'>Back to Dashboard</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
