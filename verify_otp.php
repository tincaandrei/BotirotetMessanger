<?php

session_start();

if(isset($_POST['otp'])){
    $user_otp = $_POST['otp'];
    $session_otp = $_SESSION['otp']; // Retrieve the OTP from the session

    if ($user_otp == $session_otp) {
        unset($_SESSION['otp']); 
        header("Location: chat.html"); 
        exit(); 
    } else {
        $_SESSION['error'] = 'The OTP entered is incorrect. Please try again.';
        // Redirect back to the OTP entry page if it is wrong
        header("Location: 2facodeentry.html");
        exit();
    }
} else {
    // if no otp is provided redirect to login page
    header("Location: login.html");
    exit();
}
?>
