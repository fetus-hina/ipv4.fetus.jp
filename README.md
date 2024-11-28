# ipv4.fetus.jp

[English](README.en.md)

---

[ipv4.fetus.jp](https://ipv4.fetus.jp/)のソースコードです。

地域インターネットレジストリ(RIR)から割り振りデータをダウンロードして最新情報をウェブで提供します。

ダウンロードしたデータは「開始アドレス」と「そこからの個数」で提供されていますが、それをCIDR形式に変換しています。<br>
また、隣接するブロックを統合したものや、Apache、Nginxなどのアクセス制御に使用できるテキストデータを提供します。<br>

通常はサイトで提供しているデータを利用すれば充分だと思いますが、どうしても自前で管理したいならこのソースからサーバを構築して運用することもできます。


## データの自動取得について（お願い）

アクセス間隔等についてのお願いを https://ipv4.fetus.jp/about#automation に記載しています。


## Gitによるデータ公開

https://github.com/fetus-hina/ipv4.fetus.jp-exports


## Requirements

- Linux (It might work if a Unix-like command line interface is provided)
- PHP (64bit) ≧ 8.4
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
   $ git clone https://github.com/fetus-hina/ipv4.fetus.jp.git
   $ cd ipv4.fetus.jp
   $ touch .production
   $ make
   $ ./yii migrate/up --interactive=0
   ```

4. 初回データ更新をします(30分くらいかかります)  
   ```bash
   $ ./yii update
   ```

5. ウェブサーバをいいかんじにセットアップします


## バージョンアップ

```bash
$ git fetch --prune origin
$ git merge --ff-only origin/master
$ make
$ ./yii migrate/up --interactive=0
```


## 定期データ更新（サーバ側）

cronやsystemd timerを使用して、1日1回程度次のコマンドを実行してください。

```bash
$ /path/to/yii update --interactive=0
```

## License

Copyright (C) AIZAWA Hina  
MIT License
