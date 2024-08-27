<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserType'])) {
    header("Location: login.php");
    exit();
}

// Fetch all bikes from the database
$result = $conn->query("SELECT * FROM Bikes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Bikes</title>
</head>
<body>
    <h2>List of Bikes</h2>
    <table border="1">
        <tr>
            <th>Bike ID</th>
            <th>Renting Location</th>
            <th>Description</th>
            <th>Cost Per Hour</th>
            <th>Status</th>
            <?php if ($_SESSION['UserType'] == 'Administrator'): ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['BikeID']); ?></td>
                <td><?php echo htmlspecialchars($row['RentingLocation']); ?></td>
                <td><?php echo htmlspecialchars($row['Description']); ?></td>
                <td><?php echo htmlspecialchars($row['CostPerHour']); ?></td>
                <td><?php echo htmlspecialchars($row['Status']); ?></td>
                <?php if ($_SESSION['UserType'] == 'Administrator'): ?>
                    <td>
                        <a href="edit_bike.php?id=<?php echo $row['BikeID']; ?>">Edit</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
