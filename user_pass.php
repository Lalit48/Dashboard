<?php
// Database configuration
$host = 'sql103.infinityfree.com';
$dbname = 'if0_38648328_admin_panel';
$username = 'if0_38648328';
$password = 'usPDPBk9rkUQ';


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User data
$new_username = 'user';
$new_password = '112'; // Replace with the actual password

// Hash the password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $new_username, $hashed_password);

// Execute the statement
if ($stmt->execute()) {
    echo "New user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
