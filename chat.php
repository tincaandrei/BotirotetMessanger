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
    <script src="https://cdn.socket.io/4.6.1/socket.io.min.js"></script> <!-- Include Socket.IO client library -->
</head>
<body>
    <div class="container">
        <!-- Sidebar cu prieteni -->
        <div class="sidebar">
            <!-- Username -->
            <div class="username" id="username">Welcome back, </div>

            <!-- Bara de căutare -->
            <div class="search-bar">
                <input type="text" id="user-search-input" placeholder="Search friends...">
                <span class="search-icon">🔍</span>
            </div>

            <!-- Lista de prieteni -->
            <div class="friends" id="user-list">
                <!-- Prieteni adăugați dinamic -->
            </div>
        </div>
        
        <!-- Zona de chat -->
        <div class="chat-area">
    <!-- Buton de log off -->

    <!-- Header: prieten selectat -->
    <div class="header">
    <div class="selected-friend" id="selected-friend">Selected friend: None</div>
    <button id="logout-btn" class="logout-button">Log Off</button>
</div>


    <!-- Mesaje -->
    <div class="chat-box" id="chat-box">
        <!-- Mesaje adăugate dinamic -->
    </div>

    <!-- Zona de scriere mesaje -->
    <div class="message-input">
        <input type="text" id="message-input" placeholder="Type a message...">
    </div>
</div>

<script src="logout.js"></script>


    <!-- Script pentru funcționalități -->
    <script>
        const userId = <?php echo $_SESSION['user_id']; ?>;  // Injected by PHP
        let selectedFriendId = null;

        // Fetch user data (backend Node.js)
        fetch('http://localhost:3000/getUserData', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('username').innerText += ` ${data.userData}`;
        })
        .catch(err => console.error('Error fetching user data:', err));

        // Fetch friends list
        fetch('http://localhost:3000/getAllUsers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            const userListContainer = document.getElementById('user-list');

            // Populează lista cu prieteni
            data.users.forEach(user => {
                const userElement = document.createElement('div');
                userElement.classList.add('friend');
                userElement.innerHTML = `<span>${user.username}</span>`;
                userElement.dataset.userId = user.id;
                userListContainer.appendChild(userElement);

                // Selectare prieten
                userElement.addEventListener('click', () => {
                    startChatWithUser(user.id, user.username);
                });
            });
        })
        .catch(err => console.error('Error fetching user list:', err));

        // Selectare prieten și încărcare mesaje
        function startChatWithUser(otherUserId, otherUsername) {
            selectedFriendId = otherUserId;

            document.getElementById('selected-friend').innerText = `Selected friend: ${otherUsername}`;
            document.getElementById('chat-box').innerHTML = '';

            fetch('http://localhost:3000/getMessages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId, friendId: otherUserId })
            })
            .then(response => response.json())
            .then(data => {
                const chatBox = document.getElementById('chat-box');
                data.messages.forEach(msg => {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('message', msg.sender_id === userId ? 'sent' : 'received');
                    messageElement.innerText = msg.text;
                    chatBox.appendChild(messageElement);
                });
            })
            .catch(err => console.error('Error fetching messages:', err));
        }

        // Trimitere mesaj
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const messageInput = document.getElementById('message-input');
                const message = messageInput.value.trim();

                if (!selectedFriendId || !message) {
                    alert('Please select a friend and type a message.');
                    return;
                }

                // Emit message to server
                socket.emit('chatMessage', {
                    senderId: userId,
                    receiverId: selectedFriendId,
                    text: message
                });

                // Afișare mesaj trimis
                const chatBox = document.getElementById('chat-box');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'sent');
                messageElement.innerText = message;
                chatBox.appendChild(messageElement);

                messageInput.value = ''; // Golește câmpul de input
            }
        });

        // Socket.IO initialization
        const socket = io('http://localhost:3000', { transports: ['websocket'] });

        socket.on('connect', () => {
            console.log(`Connected to Socket.IO server with ID: ${socket.id}`);
            socket.emit('joinRoom', userId);
        });

        socket.on('message', (data) => {
            if (data.senderId === selectedFriendId) {
                const chatBox = document.getElementById('chat-box');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'received');
                messageElement.innerText = data.text;
                chatBox.appendChild(messageElement);
            }
        });
    </script>
</body>
</html>
