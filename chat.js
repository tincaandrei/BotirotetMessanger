const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const bodyParser = require('body-parser');
const mysql = require('mysql');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*', // Allow all origins
        methods: ['GET', 'POST']
    }
});

// Middleware
app.use(cors());
app.use(bodyParser.json());

// MySQL connection
const connection = mysql.createConnection({
    host: 'localhost',
    port: 3306,
    database: 'user_database',
    user: 'root',
    password: ''
});

connection.connect((err) => {
    if (err) {
        console.log('Error connecting to database:', err);
        process.exit(1);
    } else {
        console.log('Successfully connected to the database');
    }
});

// Socket.IO connection
io.on('connection', (socket) => {
    console.log(`A user connected: ${socket.id}`);

    // Listen for a user joining their personal "room"
    socket.on('joinRoom', (userId) => {
        console.log(`User with ID ${userId} joined their personal room.`);
        socket.join(userId); // Join a room named after the user's ID
    });

    // Handle incoming chat messages
    socket.on('chatMessage', ({ senderId, receiverId, text }) => {
        console.log(`Message from ${senderId} to ${receiverId}: ${text}`);

        // Send the message to the recipient's room
        io.to(receiverId).emit('message', { senderId, text });
    });

    // Handle disconnect
    socket.on('disconnect', () => {
        console.log(`User disconnected: ${socket.id}`);
    });
});


// API endpoints
app.post('/getUserData', (req, res) => {
    const userId = req.body.user_id;
    if (!userId) {
        return res.status(400).json({ error: 'User ID is required' });
    }

    connection.query('SELECT * FROM users WHERE id = ?', [userId], (err, results) => {
        if (err) {
            console.error('Database error:', err);
            return res.status(500).json({ error: 'Database failure', details: err });
        }

        if (results.length > 0) {
            const username = results[0].username;
            res.json({ userData: username });
        } else {
            res.status(404).json({ error: 'User not found' });
        }
    });
});

app.post('/getAllUsers', (req, res) => {
    const currentUserId = req.body.user_id;

    if (!currentUserId) {
        return res.status(400).json({ error: 'User ID is required' });
    }

    connection.query('SELECT id, username FROM users WHERE id != ?', [currentUserId], (err, results) => {
        if (err) {
            console.error('Database error:', err);
            return res.status(500).json({ error: 'Database failure', details: err });
        }

        res.json({ users: results });
    });
});

// Start the server
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
