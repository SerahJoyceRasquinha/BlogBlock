<?php
// Database connection details
$servername = "localhost";
$username = "root";       // your DB username
$password = "";           // your DB password
$dbname = "blogblock";

// Manager credentials to insert
$managerUsername = "admin";
$plainPassword = "admin123";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Prepare and execute insert statement
$sql = "INSERT INTO manager_credentials (username, password, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $managerUsername, $hashedPassword);

if ($stmt->execute()) {
    echo "Manager inserted successfully!";
} else {
    echo "Error inserting manager: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
