<?php
session_start();
require 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    echo "User not logged in. Redirecting to login page.";
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['UserID'];

// Fetch past rentals
$stmt = $conn->prepare("SELECT Rentals.RentalID, Bikes.BikeID, Bikes.RentingLocation, Bikes.Description, Rentals.StartDateTime, Rentals.EndDateTime
                        FROM Rentals
                        JOIN Bikes ON Rentals.BikeID = Bikes.BikeID
                        WHERE Rentals.UserID = ? AND Rentals.EndDateTime IS NOT NULL");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$rentals = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Rentals</title>
</head>
<body>
    <h2>Past Rentals</h2>
    <table border="1">
        <tr>
            <th>Rental ID</th>
            <th>Bike ID</th>
            <th>Renting Location</th>
            <th>Description</th>
            <th>Start Date and Time</th>
            <th>End Date and Time</th>
        </tr>
        <?php foreach ($rentals as $rental): ?>
            <tr>
                <td><?php echo $rental['RentalID']; ?></td>
                <td><?php echo $rental['BikeID']; ?></td>
                <td><?php echo $rental['RentingLocation']; ?></td>
                <td><?php echo $rental['Description']; ?></td>
                <td><?php echo $rental['StartDateTime']; ?></td>
                <td><?php echo $rental['EndDateTime']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
