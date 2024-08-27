<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch all rentals for the user
$stmt = $conn->prepare("SELECT Rentals.*, Bikes.RentingLocation, Bikes.Description FROM Rentals JOIN Bikes ON Rentals.BikeID = Bikes.BikeID WHERE UserID = ?");
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
    <title>Rental History</title>
</head>
<body>
    <h1>Your Rental History</h1>
    <table>
        <thead>
            <tr>
                <th>Rental ID</th>
                <th>Bike</th>
                <th>Start Date/Time</th>
                <th>End Date/Time</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rentals as $rental): ?>
                <tr>
                    <td><?= $rental['RentalID'] ?></td>
                    <td><?= $rental['RentingLocation'] ?> - <?= $rental['Description'] ?></td>
                    <td><?= $rental['StartDateTime'] ?></td>
                    <td><?= $rental['EndDateTime'] ? $rental['EndDateTime'] : 'In Progress' ?></td>
                    <td><?= $rental['TotalCost'] ? '$' . $rental['TotalCost'] : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="user_dashboard.php">Back to Dashboard</a>
</body>
</html>
