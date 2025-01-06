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
            <div class="username" id="username">Welcome back, </div>
            <div class="friends" id="user-list">
                <!-- List of friends to be dynamically added here -->
            </div>
        </div>
        
        <!-- Zona de chat -->
        <div class="chat-area">
            <!-- Bara de căutare și imagine profil -->
            <div>
                <input type="checkbox" class="checkbox" id="chk" />
                <label class="label" for="chk">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                    <div class="ball"></div>
                </label>
            </div>
            <div class="header">
                <div class="selected-friend" id="selected-friend">Selected friend: None</div>
                <div class="search-bar">
                    <input type="text" id="user-search-input" placeholder="Search...">
                    <span class="search-icon">🔍</span>
                </div>
            </div>

            <!-- Mesaje -->
            <div class="chat-box" id="chat-box">
                <!-- Messages will be dynamically added here -->
            </div>
            
            <!-- Zona de scriere mesaje -->
            <div class="message-input">
                <input type="text" id="message-input" placeholder="Type a message...">
            </div>
        </div>
    </div>

    <script>
        const userId = <?php echo $_SESSION['user_id']; ?>;  // Injected by PHP
        let selectedFriendId = null; // ID of the friend to chat with
        let selectedFriendName = null; // Name of the friend

        // Fetch user data from the Node.js backend
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

        // Fetch all users for the friends list
        fetch('http://localhost:3000/getAllUsers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            const userListContainer = document.getElementById('user-list');

            // Populate the user list dynamically
            data.users.forEach(user => {
                const userElement = document.createElement('div');
                userElement.classList.add('friend');
                userElement.innerText = user.username;
                userElement.dataset.userId = user.id; // Store user ID for later use
                userListContainer.appendChild(userElement);

                // Attach the click event handler
                userElement.addEventListener('click', () => {
                    startChatWithUser(user.id, user.username);
                });
            });
        })
        .catch(err => console.error('Error fetching user list:', err));

        // Initialize Socket.IO
        const socket = io('http://localhost:3000', { transports: ['websocket'] });

        socket.on('connect', () => {
            console.log(`Connected to Socket.IO server with ID: ${socket.id}`);
            socket.emit('joinRoom', userId); // Join the room named after the user's ID
        });

        // Handle connection errors
        socket.on('connect_error', (err) => console.error('Connection error:', err));

        // Select a friend to chat with
        function startChatWithUser(otherUserId, otherUsername) {
            selectedFriendId = otherUserId;
            selectedFriendName = otherUsername;

            document.getElementById('selected-friend').innerText = `Selected friend: ${otherUsername}`;
            document.getElementById('chat-box').innerHTML = ''; // Clear chat for new conversation
        }

        // Send a message
        document.getElementById('message-input').addEventListener('keypress', function(e){
            if(e.key === 'Enter'){
                const messageInput = document.getElementById('message-input');
                const message = messageInput.value.trim();

                if (!selectedFriendId || !message) {
                    alert('Please select a friend and enter a message.');
                    return;
                }

                // Emit the message to the server
                socket.emit('chatMessage', {
                    senderId: userId,
                    receiverId: selectedFriendId,
                    text: message
                });

                // Display the sent message in the chat box
                const chatBox = document.getElementById('chat-box');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'sent');
                messageElement.innerText = message;
                chatBox.appendChild(messageElement);

                // Clear the message input
                messageInput.value = '';
            }
        });

        // Receive a message
        socket.on('message', (data) => {
            console.log('Message received:', data);

            // Display the message only if it's from the selected friend
            if (data.senderId === selectedFriendId) {
                const chatBox = document.getElementById('chat-box');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'received');
                messageElement.innerText = data.text;
                chatBox.appendChild(messageElement);
            }
        });

        
    </script>

    <style>
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 10px;
            max-width: 60%;
            word-wrap: break-word;
        }
        .sent {
            background-color: #d1e7dd;
            text-align: right;
            margin-left: auto;
        }
        .received {
            background-color: #f8d7da;
            text-align: left;
            margin-right: auto;
        }
        .friend {
            padding: 10px;
            cursor: pointer;
            border: 1px solid #ccc;
            margin: 5px 0;
            border-radius: 5px;
        }
        .friend:hover {
            background-color: #f0f0f0;
        }

        @import url('https://fonts.googleapis.com/css?family=Muli&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            background-color: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            transition: background 0.2s linear;
        }

        body.dark {
            background: #292C35;
        }

        .checkbox {
            opacity: 0;
            position: absolute;
        }

        .label {
            background-color: #111;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            position: relative;
            height: 13px;
            width: 25px;
            transform: scale(1.5);
            margin-left: 1100px;
            margin-top: 10px;
        }

        .label .ball {
            background-color: #fff;
            border-radius: 50%;
            position: absolute;
            top: 1px;
            left: 1px;
            height: 11px;
            width: 11px;
            transform: translateX(0px);
            transition: transform 0.2s linear;
        }

        .checkbox:checked + .label .ball {
            transform: translateX(12px);
        }


        .fa-moon {
            color: #f1c40f;
        }

        .fa-sun {
            color: #f39c12;
        }
    </style>
</body>
</html>