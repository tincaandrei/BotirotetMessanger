


document.querySelector('#register-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Previne trimiterea default a formularului
    const formData = new FormData(this);
    
    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Verifică dacă răspunsul este OK înainte de a îl converti în JSON
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const messageContainer = document.querySelector('.message-container');
        messageContainer.innerHTML = ''; // Curăță conținutul anterior

        if (data.status === 'error') {
            messageContainer.innerHTML = `<div class="error-message">${data.message}</div>`;
            messageContainer.classList.add('visible');
           
        } else {
            messageContainer.innerHTML = `<div class="success-message">${data.message}</div>`;
            messageContainer.classList.add('visible');
            
        }
        messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
    })
    .catch(error => {
        console.error('Error:', error);
        const messageContainer = document.querySelector('.message-container');
        messageContainer.innerHTML = `<div class="error-message">An unexpected error occurred. Please try again later.</div>`;
        messageContainer.classList.add('visible');
        messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
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

