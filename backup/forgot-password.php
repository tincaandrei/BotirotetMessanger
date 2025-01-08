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

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
    
        $title = "Password Reset OTP";
        $message = "Your one-time password for resetting your password is: $otp";
        mail($email, $title, $message);
    
        $response = ["status" => "success", "message" => "", "otp" => $otp];
    } else {
        $response = ["status" => "error", "message" => "Email address not found."];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>
