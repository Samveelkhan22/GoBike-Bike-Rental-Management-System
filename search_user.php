<?php
// Include the database connection file
require_once 'database.php';

session_start();

// Check if the user is an admin
if ($_SESSION['UserType'] != 'Administrator') {
    header("Location: login.php");
    exit();
}

// Handle search query
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchTerm = $_POST['search_term'];

    // Prepare SQL statement to search users
    $stmt = $conn->prepare("SELECT UserID, Name, Surname, Phone, Email, UserType FROM Users WHERE Name LIKE ? OR Surname LIKE ? OR Email LIKE ?");
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $searchResults = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search User</title>
</head>
<body>
    <h2>Search Users</h2>
    <form action="search_user.php" method="post">
        <label for="search_term">Search Term:</label><br>
        <input type="text" id="search_term" name="search_term" required><br><br>
        <input type="submit" value="Search">
    </form>

    <?php if (!empty($searchResults)): ?>
    <h3>Search Results</h3>
    <table border="1">
        <tr>
            <th>UserID</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Phone</th>
            <th>Email</th>
            <th>UserType</th>
        </tr>
        <?php while ($row = $searchResults->fetch_assoc()): ?>
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
    <?php endif; ?>
</body>
</html>
