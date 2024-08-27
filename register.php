<?php
session_start(); // Start the session

include 'database.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $userType = $_POST['userType']; // Administrator or User

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Users (Name, Surname, Phone, Email, UserType) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $surname, $phone, $email, $userType);

    // Execute statement
    if ($stmt->execute()) {
        // Fetch the last inserted UserID
        $userID = $stmt->insert_id;

        // Set session variables
        $_SESSION['UserID'] = $userID;
        $_SESSION['Name'] = $name;
        $_SESSION['UserType'] = $userType;

        // Debugging: Verify the session variables
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";

        // Redirect to the appropriate dashboard
        if ($userType == 'Administrator') {
            header("Location: admin_dashboard.php");
        } else  {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close the connection at the end of the script
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GoBike</title>
</head>
<body>
    <h1>Register for GoBike</h1>
    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" required><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="userType">User Type:</label>
        <select id="userType" name="userType" required>
            <option value="User">User</option>
            <option value="Administrator">Administrator</option>
        </select><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
