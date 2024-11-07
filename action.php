<?php
$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$actionType = 'approve';
$hashedPassword = password_hash('your-action-password', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO settings (action_type, action_password) VALUES (?, ?)");
$stmt->bind_param("ss", $actionType, $hashedPassword);
$stmt->execute();

$actionType = 'reject';
$hashedPassword = password_hash('another-action-password', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO settings (action_type, action_password) VALUES (?, ?)");
$stmt->bind_param("ss", $actionType, $hashedPassword);
$stmt->execute();

echo "Action passwords inserted successfully.";

$stmt->close();
$conn->close();
?>
