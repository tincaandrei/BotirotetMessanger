document.addEventListener('DOMContentLoaded', function () {
    // Reference to the eye icon and password input field
    const eyeIcon = document.getElementById('eye-icon');
    const eyeIcon_2 = document.getElementById('eye-icon_2');
    const passwordInput = document.querySelector('input[name="password"]');
    const conf_passwordInput = document.querySelector('input[name="confirm_password"]');

    // Event listener to toggle password visibility when the eye icon is clicked
    eyeIcon.addEventListener('mousedown', function () {
        // Reveal the password when cursor is over the icon
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text'; // Show the password
            eyeIcon.innerHTML = '&#128064'; // Change to closed eye (with slash)
        }
    });

    eyeIcon.addEventListener('mouseup', function () {
        // Hide the password when cursor leaves the icon
        if (passwordInput.type === 'text') {
            passwordInput.type = 'password'; // Hide the password
            eyeIcon.innerHTML = '&#128065;'; // Change to open eye
        }
    });


    eyeIcon_2.addEventListener('mousedown', function () {
        // Reveal the password when cursor is over the icon
        if (conf_passwordInput.type === 'password') {
            conf_passwordInput.type = 'text'; // Show the password
            eyeIcon_2.innerHTML = '&#128064'; // Change to closed eye (with slash)
        }
    });

    eyeIcon_2.addEventListener('mouseup', function () {
        // Hide the password when cursor leaves the icon
        if (conf_passwordInput.type === 'text') {
            conf_passwordInput.type = 'password'; // Hide the password
            eyeIcon_2.innerHTML = '&#128065;'; // Change to open eye
        }
    });
});

document.getElementById("id-mail-sent").addEventListener("click", function () {
    const mailSentIcon = document.getElementById("id-mail-sent");
    const countdownContainer = document.getElementById("countdown-container");
    const countdownText = document.getElementById("countdown-text");
    const countdownTimer = document.getElementById("countdown-timer");

    // Change the icon to a check mark
    mailSentIcon.innerHTML = "&#10003;"; // Unicode for check mark (✔)
    mailSentIcon.style.pointerEvents = "none"; // Disable further clicks during timeout

    // Show the countdown container
    countdownContainer.style.display = "block";

    let countdown = 30;
    countdownTimer.innerText = countdown;

    // Start the countdown
    const interval = setInterval(() => {
        countdown--;
        countdownTimer.innerText = countdown;

        if (countdown === 0) {
            clearInterval(interval); // Stop the countdown
            countdownContainer.style.display = "none"; // Hide the countdown text
            mailSentIcon.innerHTML = "&#128232;"; // Revert to mail icon (📬)
            mailSentIcon.style.pointerEvents = "auto"; // Re-enable the icon click
        }
    }, 1000); // Update every second
});

