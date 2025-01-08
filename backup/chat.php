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

            </div>
        </div>
        
        <!-- Zona de chat -->
        <div class="chat-area">


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


        fetch('http://localhost:3000/getAllUsers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            const userListContainer = document.getElementById('user-list');


                userElement.addEventListener('click', () => {
                    startChatWithUser(user.id, user.username);
                });
            });
        })
        .catch(err => console.error('Error fetching user list:', err));


                    const messageElement = document.createElement('div');
                    messageElement.classList.add('message', msg.sender_id === userId ? 'sent' : 'received');
                    messageElement.innerText = msg.text;
                    chatBox.appendChild(messageElement);
                });
            })

                const messageInput = document.getElementById('message-input');
                const message = messageInput.value.trim();

                if (!selectedFriendId || !message) {

                socket.emit('chatMessage', {
                    senderId: userId,
                    receiverId: selectedFriendId,
                    text: message
                });


                const chatBox = document.getElementById('chat-box');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'sent');
                messageElement.innerText = message;

