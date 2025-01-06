document.getElementById("id-mail-sent").addEventListener("click", function () {
    const emailInput = document.querySelector('input[name="email"]');
    const email = emailInput.value;
    const mailSentIcon = document.getElementById("id-mail-sent");
    const countdownContainer = document.getElementById("countdown-container");
    const countdownTimer = document.getElementById("countdown-timer");
    const messageContainer = document.querySelector('.message-container');

    if (!email) {
        alert("Please enter your email address.");
        return;
    }

    fetch('forgot-password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `email=${encodeURIComponent(email)}`
    })
        .then(response => response.json())
        .then(data => {
            messageContainer.innerHTML = '';
            if (data.status === 'success') {
                mailSentIcon.innerHTML = "&#10003;";
                mailSentIcon.style.pointerEvents = "none";
                sessionStorage.setItem('otp', data.otp);

                countdownContainer.style.display = "block";
                let countdown = 30;
                countdownTimer.innerText = countdown;

                const interval = setInterval(() => {
                    countdown--;
                    countdownTimer.innerText = countdown;

                    if (countdown === 0) {
                        clearInterval(interval);
                        countdownContainer.style.display = "none";
                        mailSentIcon.innerHTML = "&#128232;";
                        mailSentIcon.style.pointerEvents = "auto";
                        messageContainer.innerHTML = '';
                    }
                }, 1000);

                
                messageContainer.innerHTML = `<div class="success-message">${data.message}</div>`;
            } else {
                
                messageContainer.innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageContainer.innerHTML = `<div class="error-message">An unexpected error occurred. Please try again later.</div>`;
        });
});

document.addEventListener('DOMContentLoaded', function () {
    const otpInput = document.getElementById('otp-input');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    const submitButton = document.querySelector('.submit-button');
    const messageContainer = document.querySelector('.message-container');

    let isOtpValid = false; 
    let arePasswordsMatching = false; 


    otpInput.addEventListener('input', function () {
        const enteredOtp = otpInput.value;
        const correctOtp = sessionStorage.getItem('otp');

        if (enteredOtp.length === 6) {
            if (enteredOtp === correctOtp) {
                otpInput.style.backgroundColor = "lightgreen"; 
                isOtpValid = true;
            } else {
                otpInput.style.backgroundColor = "lightcoral";
                isOtpValid = false;
            }
        } else {
            otpInput.style.backgroundColor = "";
            isOtpValid = false;
        }

        validateForm();
    });

    passwordInput.addEventListener('input', checkPasswords);
    confirmPasswordInput.addEventListener('input', checkPasswords);

    function checkPasswords() {
        if (passwordInput.value === confirmPasswordInput.value && passwordInput.value.length > 0) {
            passwordInput.style.backgroundColor = "lightgreen";
            confirmPasswordInput.style.backgroundColor = "lightgreen";
            arePasswordsMatching = true;
        } else {
            passwordInput.style.backgroundColor = ""; 
            confirmPasswordInput.style.backgroundColor = ""; 
            arePasswordsMatching = false;
        }

        validateForm();
    }


    function validateForm() {
        if (isOtpValid && arePasswordsMatching) {
            submitButton.disabled = false; 
        } else {
            submitButton.disabled = true; 
        }
    }

    document.querySelector('form').addEventListener('submit', function (event) {
        if (!isOtpValid) {
            event.preventDefault();
            messageContainer.innerHTML = `<div class="error-message">Invalid OTP code.</div>`;
        } else if (!arePasswordsMatching) {
            event.preventDefault();
            messageContainer.innerHTML = `<div class="error-message">Passwords do not match.</div>`;
        } else {
            messageContainer.innerHTML = ''; 
        }
    });
});

document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault();

    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');

    const formData = new FormData();
    formData.append('email', emailInput.value);
    formData.append('password', passwordInput.value);

    fetch('verify-otp-pass.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            const messageContainer = document.querySelector('.message-container');
            messageContainer.innerHTML = '';

            if (data.status === 'success') {
                messageContainer.innerHTML = `<div class="success-message">${data.message}</div>`;
                messageContainer.scrollIntoView({ behavior: 'smooth' });
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 1500); 
            } else {
                messageContainer.innerHTML = `<div class="error-message">${data.message}</div>`;
                messageContainer.scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const messageContainer = document.querySelector('.message-container');
            messageContainer.innerHTML = `<div class="error-message">An unexpected error occurred. Please try again later.</div>`;
        });
});

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