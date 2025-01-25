# 環境構築

`.env`ファイル作成

```sh
DATABASE_URL=postgresql://johndoe:postgres@db:5432/mydb
PHP_PORT=80
EXPRESS_PORT=3000
EXPRESS_URL=http://localhost:3000
CORS_URL=http://localhost
DB_REPLICAS=1 # 起動しない場合は0
# INITDB_VOLUME=./tmp:/docker-entrypoint-initdb.d
```

初回は以下を実行

```sh
docker compose build
docker compose run --rm express npm i
docker compose up -d
```

初回以降は以下を実行

```sh
docker compose up -d
```

終了する時は以下を実行

```sh
docker compose down
```

テスト実行

`docker compose up -d`で起動した状態で以下を実行

```sh
# テスト用のディレクトリに移動
cd playwright
# テスト用のパッケージをインストール    
npm i
# playwrightのパッケージをインストール
npm run test:install
# テストを実行
npm run test
```
