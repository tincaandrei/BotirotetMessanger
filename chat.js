
const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const app = express();
//using axios for testing the database conection with 'hardocded' values
const axios = require('axios');

// mikey mouse solution to use both php and nodeJs 
const cors = require('cors');
app.use(cors());  


app.use(bodyParser.json());

const connection = mysql.createConnection({
    host: 'localhost',
    port: 3306,
    database: 'user_database',
    user: 'root',
    password:''
});

connection.connect((err) => {
    if(err){
        console.log("error while connecting to the users_database");
        process.exit(1);
    }else{
        console.log("succesfuly connected");

    }
});

// endpoint to fetch the user data based on the userId from the chat.php

app.post('/getUserData', (req, res) => {
    console.log('Request Body:', req.body); // Log the request body

    const userId = req.body.user_id; // Get the user ID from the body
    if (!userId) {
        return res.status(400).json({ error: "User ID is required" }); // Return an error if no user_id is provided
    }

    console.log('User ID:', userId);  // Log userId to confirm it's received properly

    connection.query('SELECT * FROM users WHERE id = ?', [userId], (err, results) => {
        if (err) {
            console.error('Database error:', err); // Log database error
            return res.status(500).json({ error: "Database failure", details: err });
        }

        
        if (results.length > 0) {
            const user_name = results[0].username;
            console.log('Sending user name :', user_name);
            return res.json({ userData: user_name });
        } else {
            // Return a "User not found" message if no user matches
            console.log('No user found');
            return res.status(404).json({ error: "User not found!" });
        }
    });
});

const PORT = 3000; 
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
