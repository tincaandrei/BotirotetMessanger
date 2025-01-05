<?php
session_start();
$user_id = $_SESSION['user_id'];
?>


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar cu prieteni -->
        <div class="sidebar">
            <div class="username" id="username">Welcome back, </div>
            <div class="friends">
                <div class="friend">PersoanaX</div>
                <div class="friend">PersoanaY</div>
            </div>
        </div>
        
        <!-- Zona de chat -->
        <div class="chat-area">
            <!-- Bara de căutare și imagine profil -->
            <div class="header">
                <div class="search-bar">
                    <input type="text" placeholder="Caută...">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="profile-pic">
                    <img src="botirotet.png" alt="Profile Picture">
                </div>
            </div>

            <!-- Mesaje -->
            <div class="chat-box" id="chat-box">
                <!-- Mesajele vor fi adăugate dinamic aici -->
            </div>
            
            <!-- Zona de scriere mesaje -->
            <div class="message-input">
                <span class="emoji">😊</span>
                <input type="text" id="message-input" placeholder="Type a message...">
                <button type="submit" id="send-btn">Send</button>
            </div>
        </div>
    </div>
    <script>
        const userId = <?php echo $_SESSION['user_id']; ?>;  // Injected by PHP

        // Fetch user data from the Node.js backend
        fetch('http://localhost:3000/getUserData', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); // Parse the response if it's successful
        })
        .then(data =>{
            const displayed_username = data.userData;
            document.getElementById('username').innerText += ' ' + displayed_username;
            console.log('recieved username is :', displayed_username)
        })
        .catch(err => {
            console.error('Error fetching user data:', err);
        });
            </script>
        </body>
</html>


