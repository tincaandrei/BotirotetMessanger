


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification</title>
    <link rel="stylesheet" href="2facodeentry.css">
</head>
<body>
    <div class="container">
        <div class="icon">
            <img src="https://img.icons8.com/ios-filled/50/000000/lock--v1.png" alt="lock icon">
        </div>
        <h2>2-step verification</h2>
        <p>We sent a verification code to your email.<br>Please enter the code in the field below.</p>
        <!-- PHP code for displaying the error message in case the users misspells the 2fa code -->
        <?php
            session_start(); // Start the session
            if(isset($_SESSION['error'])):?>
                <div style="color :red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif;?> 

        <form class="code-entry-form" action="verify_otp.php" method="post">
            <div class="code-input-container">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
                <input type="text" name="otp[]" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]" required title="Please enter a number between 0 and 9">
            </div>
            <button type="submit" class="verify-button">Log in</button>
        </form>
    </div>
    <script src="2facodeentry.js"></script>
</body>
</html>
