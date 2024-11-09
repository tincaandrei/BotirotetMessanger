<?php

session_start();

if(isset($_POST['otp'])){
    $user_otp = implode('', $_POST['otp']); // converting form string to array
    $session_otp = $_SESSION['otp']; // Retrieve the OTP from the session

    if ($user_otp == $session_otp) {
        unset($_SESSION['otp']); 
        header("Location: chat.html"); 
        exit(); 
    } else {
       
        // Redirect back to the OTP entry page if it is wrong
        
        $_SESSION['error'] = 'The OTP entered is incorrect. Please try again.';
        header("Location: 2facodeentry.php");
        exit();
        //              PRINTS USED FOR DEBUGGING    
    }
} else {
    // if no otp is provided redirect to login page
    header("Location: login.html");
    exit();
}
?>
