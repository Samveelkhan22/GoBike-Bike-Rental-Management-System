<?php
// Include the database connection file
require_once 'database.php';

session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$searchResult = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the search parameters
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Prepare an SQL statement to search for bikes
    $stmt = $conn->prepare("SELECT * FROM Bikes WHERE RentingLocation LIKE ? AND Status = ?");
    $location = "%$location%";
    $stmt->bind_param("ss", $location, $status);

    // Execute the statement and store the result
    $stmt->execute();
    $searchResult = $stmt->get_result();

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Bike</title>
</head>
<body>
    <h2>Search for a Bike</h2>
    <form action="search_bike.php" method="post">
        <label for="location">Renting Location:</label><br>
        <input type="text" id="location" name="location"><br><br>
        <label for="status">Status:</label><br>
        <select id="status" name="status">
            <option value="Available">Available</option>
            <option value="Rented">Rented</option>
        </select><br><br>
        <input type="submit" value="Search">
    </form>

    <?php if ($searchResult): ?>
        <h3>Search Results:</h3>
        <table border="1">
            <tr>
                <th>Bike ID</th>
                <th>Renting Location</th>
                <th>Description</th>
                <th>Cost Per Hour</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $searchResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['BikeID']; ?></td>
                    <td><?php echo $row['RentingLocation']; ?></td>
                    <td><?php echo $row['Description']; ?></td>
                    <td><?php echo $row['CostPerHour']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                    <td>
                        <?php if ($_SESSION['UserType'] == 'Administrator'): ?>
                            <a href="edit_bike.php?id=<?php echo $row['BikeID']; ?>">Edit</a>
                        <?php elseif ($_SESSION['UserType'] == 'User' && $row['Status'] == 'Available'): ?>
                            <a href="rent_bike.php?bike_id=<?php echo $row['BikeID']; ?>">Rent</a>
                        <?php else: ?>
                            Not Available
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>
