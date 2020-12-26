var express = require('express');
var app = express();
var http = require('http').createServer(app);
var io = require('socket.io')(http, {
    cors: {
        origin: "http://localhost:8080",
        methods: ["GET", "POST"]
    }
});
const port = 3000;

io.on('connection', client => {
    client.emit('init', {data: 'hello world'})
})

app.use((req, res, next) => {
    
})

io.listen(port);

/*
app.get('/scoreboard', (req, res) => {
    console.log('getting scoreboard');
    res.end();
})

app.post('new_gamescore', (req, res) => {
    console.log('getting scoreboard');
    res.end();
})

app.get('/player:id', (req, res) => {
    console.log('getting player ', id);
    res.end();
})
*/