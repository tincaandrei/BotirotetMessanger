<?php
session_start();

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "user_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['password']);

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateSql = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
    
    if ($conn->query($updateSql) === TRUE) {
        $response = ["status" => "success", "message" => "Password updated successfully."];
    } else {
        $response = ["status" => "error", "message" => "Failed to update password."];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>
