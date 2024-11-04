


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
            messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
        } else {
            messageContainer.innerHTML = `<div class="success-message">${data.message}</div>`;
            messageContainer.classList.add('visible');
            messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const messageContainer = document.querySelector('.message-container');
        messageContainer.innerHTML = `<div class="error-message">An unexpected error occurred. Please try again later.</div>`;
        messageContainer.classList.add('visible');
        messageContainer.scrollIntoView({ behavior: 'smooth' }); // Asigură afișarea completă a mesajului
    });
});

