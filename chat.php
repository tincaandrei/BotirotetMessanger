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
    <div class="chat-box" id="chat-box" style="overflow-y:scroll;  scrollbar-width: thin;  scrollbar-color: #3a3b3c transparent;    ; scroll-behavior: smooth; max-height: 530px;">
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
                
                // Emit event to mark messages as read
        socket.emit('markAsRead', { userId: userId, friendId: otherUserId });

                // Remove unread notification badge for this friend
                const friendElement = document.querySelector(`.friend[data-user-id="${otherUserId}"]`);
                if (friendElement) {
                    const unreadBadge = friendElement.querySelector('.unread-count');
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                }

                scrollToBottom();
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

                // Remove unread notification for the recipient
                const friendElement = document.querySelector(`.friend[data-user-id="${selectedFriendId}"]`);
                if (friendElement) {
                    const unreadBadge = friendElement.querySelector('.unread-count');
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                }

                scrollToBottom();

                // Emit an event to mark messages as read
                socket.emit('markAsRead', { userId, friendId: selectedFriendId });
                    }
        });

        fetch('http://localhost:3000/getUnreadCounts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
        const userListContainer = document.getElementById('user-list');

        // Update friend list with unread counts
        data.unreadCounts.forEach(({ sender_id, unread_count }) => {
                const friendElement = document.querySelector(`.friend[data-user-id="${sender_id}"]`);
                if (friendElement) {
                    const unreadBadge = document.createElement('span');
                    unreadBadge.classList.add('unread-count');
                    unreadBadge.textContent = unread_count;
                    friendElement.appendChild(unreadBadge);
                }
        });
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

                const friendElement = document.querySelector(`.friend[data-user-id="${data.senderId}"]`);
                if (friendElement) {
                    const unreadBadge = friendElement.querySelector('.unread-count');
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                }

                scrollToBottom();

                socket.emit('markAsRead', { userId, friendId: data.senderId });
            }
        });
        socket.on('unreadCount', ({ senderId, unreadCount }) => {
    const friendElement = document.querySelector(`.friend[data-user-id="${senderId}"]`);
    if (friendElement) {
        const unreadBadge = friendElement.querySelector('.unread-count');
        if (unreadBadge) {
            unreadBadge.textContent = unreadCount;
        } else {
            const newUnreadBadge = document.createElement('span');
            newUnreadBadge.classList.add('unread-count');
            newUnreadBadge.textContent = unreadCount;
            friendElement.appendChild(newUnreadBadge);
        }
    }
});



        function scrollToBottom() {
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Search functionality for friends
document.getElementById('user-search-input').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim(); // Get the search query and convert to lowercase
    const friends = document.querySelectorAll('.friend'); // Select all friend elements

    friends.forEach(friend => {
        const username = friend.textContent.toLowerCase(); // Get the friend's name in lowercase
        if (username.includes(query)) {
            friend.style.display = 'flex'; // Show the friend if it matches the query
        } else {
            friend.style.display = 'none'; // Hide the friend if it doesn't match the query
        }
    });
});


// Adăugarea unui mesaj nou
const chatBox = document.getElementById('chat-box');
const messageElement = document.createElement('div');
messageElement.classList.add('message', 'sent');
messageElement.innerText = "Hello, this is a new message!";
chatBox.appendChild(messageElement);

// Derulăm automat
scrollToBottom();


    </script>
</body>
</html>
