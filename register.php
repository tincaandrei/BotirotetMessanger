

<?php
// Activare afișare erori pentru depanare
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'user_database');

// Verificare conexiune
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificare dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = $conn->real_escape_string($_POST['phone_number']);

    // Verificare dacă numele de utilizator există deja
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        $check_stmt->close();
        $conn->close();
        exit(); // Oprește execuția pentru a preveni inserarea
    }
    $check_stmt->close();

    // Verificare parole egale
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        $conn->close();
        exit();
    }

    // Verificare complexitate parolă (opțional)
    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long']);
        $conn->close();
        exit();
    }

    // Hash parola pentru securitate
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserare în baza de date
    $sql = "INSERT INTO users (username, email, password, phone_number) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
        $conn->close();
        exit();
    }

    $stmt->bind_param("ssss", $username, $email, $hashed_password, $phone_number);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>