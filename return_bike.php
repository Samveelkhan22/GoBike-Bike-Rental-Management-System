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
    $endDateTime = date("Y-m-d H:i:s");

    // Fetch rental details, including StartDateTime and CostPerHour from the Bikes table
    $stmt = $conn->prepare("
        SELECT Rentals.StartDateTime, Bikes.CostPerHour 
        FROM Rentals 
        JOIN Bikes ON Rentals.BikeID = Bikes.BikeID 
        WHERE Rentals.UserID = ? AND Rentals.BikeID = ? AND Rentals.EndDateTime IS NULL
    ");

    if ($stmt === false) {
        die('Prepare() failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ii", $selectedUserId, $bikeId);
    $stmt->execute();
    $stmt->bind_result($startDateTime, $costPerHour);
    $stmt->fetch();
    $stmt->close();

    if ($startDateTime && $costPerHour) {
        // Calculate total cost
        $startTime = strtotime($startDateTime);
        $endTime = strtotime($endDateTime);
        $hours = ceil(($endTime - $startTime) / 3600);
        $totalCost = $hours * $costPerHour;

        // Update rental record with end time and total cost
        $stmt = $conn->prepare("UPDATE Rentals SET EndDateTime = ?, TotalCost = ? WHERE UserID = ? AND BikeID = ? AND EndDateTime IS NULL");

        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("sdii", $endDateTime, $totalCost, $selectedUserId, $bikeId);
        $stmt->execute();
        $stmt->close();

        // Update bike status
        $stmt = $conn->prepare("UPDATE Bikes SET Status = 'Available' WHERE BikeID = ?");

        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("i", $bikeId);
        $stmt->execute();
        $stmt->close();

        // Display notification with the total cost
        echo "<script>alert('Bike returned successfully!\\nTotal Cost: $$totalCost');</script>";

        // Redirect based on user type
        if ($userType == 'Administrator') {
            header("Refresh:0; url=admin_dashboard.php");
        } else {
            header("Refresh:0; url=user_dashboard.php");
        }
        exit();
    } else {
        echo "<script>alert('No active rental found for this bike.'); window.location.href='return_bike.php';</script>";
        exit();
    }
} else {
    // Fetch rented bikes for the selected user (or current user)
    if ($userType == 'Administrator') {
        if (isset($_GET['user_id'])) {
            $selectedUserId = $_GET['user_id'];
        } else {
            $selectedUserId = null;
        }
    } else {
        $selectedUserId = $userId;
    }

    if ($selectedUserId) {
        $stmt = $conn->prepare("
            SELECT Bikes.BikeID, Bikes.RentingLocation, Bikes.Description, Bikes.CostPerHour
            FROM Rentals
            JOIN Bikes ON Rentals.BikeID = Bikes.BikeID
            WHERE Rentals.UserID = ? AND Rentals.EndDateTime IS NULL
        ");

        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("i", $selectedUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bikes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $bikes = [];
    }

    // Fetch all users if admin is logged in
    if ($userType == 'Administrator') {
        $stmt = $conn->prepare("SELECT UserID, Name, Surname FROM Users WHERE UserType = 'User'");

        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }

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
    <title>Return a Bike</title>
</head>
<body>
    <h2>Return a Bike</h2>

    <?php if ($userType == 'Administrator'): ?>
        <form method="get" action="return_bike.php">
            <label for="user_id">Select User:</label><br>
            <select id="user_id" name="user_id" required onchange="this.form.submit()">
                <option value="">--Select a User--</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['UserID']; ?>" <?php if (isset($selectedUserId) && $selectedUserId == $user['UserID']) echo 'selected'; ?>>
                        <?php echo $user['Name'] . " " . $user['Surname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form><br>
    <?php endif; ?>

    <?php if (!empty($bikes)): ?>
        <form action="return_bike.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
            <label for="bike_id">Select Bike:</label><br>
            <select id="bike_id" name="bike_id" required>
                <?php foreach ($bikes as $bike): ?>
                    <option value="<?php echo $bike['BikeID']; ?>">
                        <?php echo $bike['RentingLocation'] . " - " . $bike['Description'] . " - $" . $bike['CostPerHour'] . "/hr"; ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <input type="submit" value="Return Bike">
        </form>
    <?php else: ?>
        <?php if (isset($selectedUserId)): ?>
            <p>No bikes currently rented by this user.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
