<?php
session_start();
require 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID']) || !isset($_SESSION['UserType'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['UserID'];
$userType = $_SESSION['UserType'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If the user is an admin, get the selected user ID
    if ($userType == 'Administrator') {
        $selectedUserId = $_POST['user_id'];
    } else {
        $selectedUserId = $userId;
    }

    $bikeId = $_POST['bike_id'];
    $startDateTime = date("Y-m-d H:i:s");

    // Fetch bike cost per hour
    $stmt = $conn->prepare("SELECT CostPerHour FROM Bikes WHERE BikeID = ? AND Status = 'Available'");
    $stmt->bind_param("i", $bikeId);
    $stmt->execute();
    $stmt->bind_result($costPerHour);
    $stmt->fetch();
    $stmt->close();

    if ($costPerHour) {
        // Insert rental record
        $stmt = $conn->prepare("INSERT INTO Rentals (UserID, BikeID, StartDateTime) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $selectedUserId, $bikeId, $startDateTime);
        $stmt->execute();
        $stmt->close();

        // Update bike status
        $stmt = $conn->prepare("UPDATE Bikes SET Status = 'Rented' WHERE BikeID = ?");
        $stmt->bind_param("i", $bikeId);
        $stmt->execute();
        $stmt->close();

        // Display notification with the starting date/time and cost per hour
        echo "<script>alert('Bike rented successfully!\\nStart Date/Time: $startDateTime\\nCost Per Hour: $$costPerHour');</script>";

        // Redirect based on user type
        if ($userType == 'Administrator') {
            header("Refresh:0; url=admin_dashboard.php");
        } else {
            header("Refresh:0; url=user_dashboard.php");
        }
        exit();
    } else {
        echo "<script>alert('Bike not available.'); window.location.href='rent_bike.php';</script>";
        exit();
    }
} else {
    // Fetch all available bikes
    $stmt = $conn->prepare("SELECT BikeID, RentingLocation, Description, CostPerHour FROM Bikes WHERE Status = 'Available'");
    $stmt->execute();
    $result = $stmt->get_result();
    $bikes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch all users if admin is logged in
    if ($userType == 'Administrator') {
        $stmt = $conn->prepare("SELECT UserID, Name, Surname FROM Users WHERE UserType = 'User'");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent a Bike</title>
</head>
<body>
    <h2>Rent a Bike</h2>
    <form action="rent_bike.php" method="post">
        <?php if ($userType == 'Administrator'): ?>
            <label for="user_id">Select User:</label><br>
            <select id="user_id" name="user_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['UserID']; ?>">
                        <?php echo $user['Name'] . " " . $user['Surname']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
        <?php endif; ?>

        <label for="bike_id">Select Bike:</label><br>
        <select id="bike_id" name="bike_id" required>
            <?php foreach ($bikes as $bike): ?>
                <option value="<?php echo $bike['BikeID']; ?>">
                    <?php echo $bike['RentingLocation'] . " - " . $bike['Description'] . " - $" . $bike['CostPerHour'] . "/hr"; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="submit" value="Rent Bike">
    </form>
</body>
</html>
