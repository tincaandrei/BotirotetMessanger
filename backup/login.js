document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne trimiterea formularului

    const formData = new FormData(this);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageContainer = document.querySelector('.message-container');
        messageContainer.innerHTML = '';
        if (data.status === 'success') {
            window.location.href = data.redirect; // Redirecționează către chat.php
        } else {
          messageContainer.innerHTML = `<div class="error-message">${data.message}</div>`;
          messageContainer.classList.add('visible');
          messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
        }
    })
    .catch(error => {
        console.error('Eroare:', error);
        messageContainer.innerHTML = `<div class="error-message">An unexpected error occurred. Please try again later.</div>`;
        messageContainer.classList.add('visible');
        messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
    });
});



///REMEMBER ME - CHECK BOX LISTENER
document.addEventListener('DOMContentLoaded', function () {
    // Reference to the form elements
    const usernameInput = document.querySelector('input[name="username"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const rememberCheckbox = document.querySelector('input[name="remember"]');
  
    // Load saved username and password if "Remember Me" was checked
    if (localStorage.getItem('remember') === 'true') {
      usernameInput.value = localStorage.getItem('username') || '';
      passwordInput.value = localStorage.getItem('password') || '';
      rememberCheckbox.checked = true;
    }
  
    // Event listener for form submission
    document.querySelector('form').addEventListener('submit', function (event) {
      if (rememberCheckbox.checked) {
        // Save username, password, and "remember" status in localStorage
        localStorage.setItem('username', usernameInput.value);
        localStorage.setItem('password', passwordInput.value);
        localStorage.setItem('remember', 'true');
      } else {
        // Clear data if "Remember Me" is unchecked
        localStorage.removeItem('username');
        localStorage.removeItem('password');
        localStorage.removeItem('remember');
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    // Reference to the eye icon and password input field
    const eyeIcon = document.getElementById('eye-icon');
    const passwordInput = document.querySelector('input[name="password"]');

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
});




