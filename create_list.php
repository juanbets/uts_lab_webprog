<?php
// Start session and include config file for database connection
session_start();
include 'config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission to create a new list
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $list_name = $_POST['list_name'];
    $user_id = $_SESSION['user_id'];

    // Insert the new list into the 'lists' table
    $stmt = $pdo->prepare("INSERT INTO lists (list_name, user_id) VALUES (:list_name, :user_id)");
    $stmt->bindParam(':list_name', $list_name);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Redirect to dashboard after creation
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Make sure your styles.css is linked correctly -->
</head>
<body>
    <div class="container">
        <h1>Create a New To-Do List</h1>
        <form method="POST" action="create_list.php">
            <label for="list_name">List Name:</label>
            <input type="text" id="list_name" name="list_name" required>
            <button type="submit">Create List</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a> <!-- Link to go back to dashboard -->
    </div>
</body>
</html>

