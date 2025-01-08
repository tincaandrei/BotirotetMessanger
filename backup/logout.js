// Funcționalitate pentru butonul Log Off
document.getElementById('logout-btn').addEventListener('click', () => {
    const confirmLogout = confirm('Are you sure you want to log off?');

    if (confirmLogout) {
        // Trimitem cererea de logout către backend
        fetch('logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('You have been logged out successfully.');
                // Redirecționăm utilizatorul către pagina de login
                window.location.href = 'login.html';
            } else {
                alert('Error during logout. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error during log off:', error);
            alert('An unexpected error occurred.');
        });
    }
});
