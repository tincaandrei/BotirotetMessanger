<?php
session_start();

// Conexiunea la baza de date
$servername = "localhost";
$username = "root"; // Nume utilizator pentru baza de date
$password = ""; // Parola pentru baza de date
$dbname = "user_database";

// Crearea conexiunii
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // Verificarea existenței utilizatorului și a parolei
    $sql = "SELECT * FROM users WHERE username = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['username'] = $user;
            $response = ["status" => "success", "redirect" => "chat.html"];
        } else {
            $response = ["status" => "error", "message" => "Invalid username or password!"];
        }
    } else {
        $response = ["status" => "error", "message" => "Invalid username or password!"];
    }

    // Returnează răspunsul ca JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>
