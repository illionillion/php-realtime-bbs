const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: process.env.CORS_URL || '*', // CORS_URLを環境変数から読み込む
    methods: ['GET', 'POST']
  }
});

app.get('/', (req, res) => {
  res.send('Not Found');
});

io.on('connection', (socket) => {
  console.log('A user connected');
  socket.on('new_post', (data) => {
    console.log('New post received:', data);
    io.emit('new_post', data); 
  });

  socket.on('disconnect', () => {
    console.log('User disconnected');
  });
});

server.listen(3000, () => {
  console.log('Express and Socket.io server running on port 3000');
});
