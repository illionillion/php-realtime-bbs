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

// JSONのパースを追加
app.use(express.json()); // これがないとPOSTリクエストのJSONがパースされない

// /new_post へのPOSTリクエストを処理
app.post('/new_post', (req, res) => {
  // 受け取った投稿を全てのクライアントに送信
  io.emit('new_post', req.body);
  
  res.status(200).json({ message: '送信成功' });
});

// Socket.ioの接続イベント
io.on('connection', (socket) => {
  console.log('A user connected');
  socket.on('disconnect', () => {
    console.log('User disconnected');
  });
});

server.listen(3000, () => {
  console.log('Express and Socket.io server running on port 3000');
});
