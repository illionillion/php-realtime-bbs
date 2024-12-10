<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script>
    // Socket.io接続
    const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

    // 新しい投稿を受け取ったときの処理
    socket.on('new_post', (data) => {
        addNewPost(data);
    });

    /**
     * テンプレートを使って新しい投稿を追加する関数
     * @param {Object} data - 受け取った投稿データ (name, comment, createdat)
     */
    function addNewPost(data) {
        const template = document.getElementById('post-template'); // テンプレートを取得
        const clone = template.content.cloneNode(true); // テンプレートを複製

        // 名前、コメント、時刻を動的に設定
        clone.querySelector('.post-name').textContent = data.name;
        clone.querySelector('.post-comment').textContent = data.comment;
        clone.querySelector('.post-createdat').textContent = data.createdat;

        // 投稿リストの先頭に挿入
        const postList = document.getElementById('posts');
        postList.prepend(clone);
    }
</script>