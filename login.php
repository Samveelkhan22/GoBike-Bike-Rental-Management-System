<?php
session_start();

include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $userType = $_POST['userType'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT UserID, Name, UserType FROM Users WHERE Email = ? AND UserType = ?");
    $stmt->bind_param("ss", $email, $userType);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($userID, $name, $userType);
        $stmt->fetch();

        // Set session variables
        $_SESSION['UserID'] = $userID;
        $_SESSION['Name'] = $name;
        $_SESSION['UserType'] = $userType;

        // Redirect based on user type
        if ($userType == 'Administrator') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        echo "Invalid email or user type.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GoBike</title>
</head>
<body>
    <h1>Login to GoBike</h1>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="userType">User Type:</label>
        <select id="userType" name="userType" required>
            <option value="User">User</option>
            <option value="Administrator">Administrator</option>
        </select><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
