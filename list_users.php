<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is an admin
if ($_SESSION['UserType'] != 'Administrator') {
    header("Location: login.php");
    exit();
}

// Fetch users from the database
$result = $conn->query("SELECT UserID, Name, Surname, Phone, Email, UserType FROM Users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Users</title>
</head>
<body>
    <h2>List of Users</h2>
    <table border="1">
        <tr>
            <th>UserID</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Phone</th>
            <th>Email</th>
            <th>UserType</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['UserID']); ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Surname']); ?></td>
            <td><?php echo htmlspecialchars($row['Phone']); ?></td>
            <td><?php echo htmlspecialchars($row['Email']); ?></td>
            <td><?php echo htmlspecialchars($row['UserType']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
