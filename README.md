# ipv4.fetus.jp

[ipv4.fetus.jp](https://ipv4.fetus.jp/)のソースコードです。<br>
Source code for [ipv4.fetus.jp](https://ipv4.fetus.jp/).

地域インターネットレジストリ(RIR)から割り振りデータをダウンロードして最新情報をウェブで提供します。<br>
Download allocation data from the Regional Internet Registry (RIR) and provide up-to-date information on the web.

ダウンロードしたデータは「開始アドレス」と「そこからの個数」で提供されていますが、それをCIDR形式に変換しています。<br>
また、隣接するブロックを統合したものや、Apache、Nginxなどのアクセス制御に使用できるテキストデータを提供します。<br>
The downloaded data is provided in the form of "start address" and "addresses from it", but this project converts them to CIDR format.<br>
In addition, it provides a consolidated list of adjacent blocks and address list that can be used for access control in Apache, Nginx, etc.

通常はサイトで提供しているデータを利用すれば充分だと思いますが、どうしても自前で管理したいならこのソースからサーバを構築して運用することもできます。<br>
I think that it is sufficient to use the data provided on the site as usual, but if you really want to manage it yourself, you can build and operate the server from this source.



## データの自動取得について（お願い） / About automatic data acquisition

アクセス間隔等についてのお願いを https://ipv4.fetus.jp/about#automation に記載しています。<br>
[Access intervals, etc.](https://ipv4.fetus.jp/about#automation)


## Gitによるデータ公開 / Data publication by Git

https://github.com/fetus-hina/ipv4.fetus.jp-exports


## Requirements

- Linux (It might work if a Unix-like command line interface is provided)
- PHP (64bit) ≧ 8.1
  - PHP-FPM
- Node.js (LTS or latest)
- PostgreSQL
- A web server as you like (Apache, Nginx, etc.)


## インストール（サーバ側）

1. PHP, Node.js, PostgreSQL をいいかんじにセットアップします

2. PostgreSQL に role(user), database を作成します  
   レポジトリのデフォルト設定は [config/components/db/db.php](https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/config/components/db/db.php) を参照してください  
   それとは異なる設定にすることももちろん可能です  
   設定を変更する場合は、後のステップで `clone` した後、 `make` する前に設定ファイルを調整してください

3. アプリケーションを構築します
   ```bash
   git clone https://github.com/fetus-hina/ipv4.fetus.jp.git

   cd ipv4.fetus.jp

   touch .production

   make

   ./yii migrate/up --interactive=0
   ```

4. 初回データ更新をします(30分くらいかかります)  
   ```bash
   ./yii update
   ```

5. ウェブサーバをいいかんじにセットアップします


## バージョンアップ

ファイルを変更した場合はこの通りにいきませんが、そのようなことをする人は次のコマンドの羅列を見て何をするか理解できると信じています。

```bash
git fetch --prune origin

git merge --ff-only origin/master

make

./yii migrate/up --interactive=0
```


## 定期データ更新（サーバ側）

cronやsystemd timerを使用して、1日1回程度次のコマンドを実行してください。

```bash
/path/to/yii update --interactive=0
```

## License

Copyright (C) AIZAWA Hina  
MIT License
