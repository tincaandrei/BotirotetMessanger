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
            $_SESSION['user_id'] = $row['id'];
           if($row['is_2fa_enabled'] == 1){
                $otp = rand(100000, 999999);
                
                //store the otp in a session variable
                $_SESSION['otp'] = $otp;
                //send email with the otp to the user
                
                $title = "Your OTP";
                mail($row['email'], $title, "Here is your one time login key:    $otp");

                $response = ["status" => "success", "redirect" => "2facodeentry.php"];              
            }else{
                
                $response = ["status" => "success", "redirect" => "chat.php"];      
           }
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
