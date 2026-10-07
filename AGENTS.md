# AGENTS.md

This file provides guidance to AI coding agents when working with code in this repository.

## 概要

[ipv4.fetus.jp](https://ipv4.fetus.jp/) のソースコード。5つの RIR (AfriNIC/APNIC/ARIN/LACNIC/RIPE NCC) から割り振りデータ（「開始アドレス＋個数」形式）を取得し、CIDR に変換・隣接ブロックを結合して、国別リストや Apache/Nginx 等のアクセス制御用テキストとして配信する。

技術スタック: PHP ≧ 8.4 (64bit) + Yii2 (basic app template ベース) + PostgreSQL。フロントは Bootstrap 5 / jQuery / Chart.js。名前空間 `app\` がリポジトリルートに PSR-4 マップされている。

## コマンド

```bash
make                      # 依存インストール (npm clean-install / composer install) + JS/CSS/.mo/favicon 等のビルド一式
./yii migrate/up --interactive=0
./yii update              # RIR からのデータ取得〜全派生データ再生成（初回は約30分）
./yii update 1            # 第1引数 skipUpdate: RIR 取得・merged・stat をスキップし、krfilter 以降のみ再生成

make test                 # vendor/bin/codecept run unit（XDEBUG_MODE=coverage 付き）
vendor/bin/codecept run unit helpers/IPHelperTest            # 単一テストファイル
vendor/bin/codecept run unit helpers/IPHelperTest:testFoo    # 単一テストメソッド
tests/bin/yii migrate/up --interactive=0   # テスト用 DB (ipv4test/ipv4test@localhost) へのマイグレーション

make check-style          # 以下すべて
make check-style-phpcs    # vendor/bin/phpcs（JP3CKI 規約、views/tests/gii は対象外）
make check-style-phpstan  # vendor/bin/phpstan --memory-limit=1G（level max、tests は対象外）
make check-style-composer # composer normalize --dry-run
make check-style-js       # semistandard（resources/**/*.es）
make check-style-css      # stylelint（resources/**/*.scss）
make check-style-ci       # podman で actionlint
```

- テストは DB を使う（Yii2 モジュールの `fixtures`/`orm`）。`config/test.php` + `config/components/db/test.php` を参照。
- 依存の一括更新は `./depends.sh`。デプロイは `./deploy.sh`（Deployer v6, `deploy.php`）— 実行は本番に影響するので明示的な指示がない限り行わない。

## 環境切り替え

`yii` と `web/index.php` はリポジトリ直下の `.production` ファイルの有無で判定する。存在しなければ `YII_DEBUG=true` / `YII_ENV=dev`（web は yii2-debug、console は gii が有効化される）。

## アーキテクチャ

### データ更新パイプライン（`commands/UpdateController.php`）

`./yii update` (`actionIndex`) が以下を順に実行する。サイトの全データはこのバッチで DB に事前計算されており、Web 側は基本的に読むだけ。

1. 1トランザクション内で `allocation_cidr` / `allocation_block` を全削除し、各 RIR の `delegated-*-extended-latest` をダウンロード・パースして再投入（`actionAfrinic` 等は個別実行も可）
2. `actionMerged`: 国ごとに CIDR を `bin/filter-merge-cidr`（stdin→stdout の PHP スクリプト、`proc_open` で起動）に通して結合し `merged_cidr` へ
3. `actionStat`: `region_stat` を集計
4. `actionKrfilter`: 複数国をまとめたフィルタ（krfilter/rufilter）の CIDR を生成
5. `actionIpv4bycc` / `actionNginxGeo`: 全国まとめ形式のダンプを生成（`helpers/Ipv4byccDumper.php`, `helpers/NginxGeoDumper.php`）
6. `actionPreformattedGitRepo`: `runtime/preformatted/` に全テンプレート×全国のファイルを書き出し、そこが git リポジトリなら commit & push（公開先: fetus-hina/ipv4.fetus.jp-exports）

アドレス計算のコアは `helpers/IPHelper.php`（ブロック分割）と `helpers/CountToCidr.php`。テストもこの2つが中心。

### ダウンロード形式はデータ駆動

`.txt` 出力のフォーマット（Apache/Nginx/iptables/firewalld ipset/CSV など）はコードではなく `download_template` テーブル（＋ `comment_style`, `newline`）の行として定義されており、マイグレーションで追加される。新しい出力形式の追加は通常マイグレーションで行い、`helpers/DownloadFormatter.php` が描画する。URL は `config/components/web/url-manager.php` の `<cc>.<template>.txt` / `krfilter.<id>.<template>.txt` ルールで解決される。

### モデルと Gii

`models/` の ActiveRecord は独自の Gii ジェネレータ（`gii/model/Generator.php`, テンプレート `views/gii/model`）で生成される。手書きロジックはモデル本体ではなく `models/traits/` のトレイトに置き、`#[InjectTo([Region::class])]` 属性（`attributes/InjectTo.php`）を付けると Gii が再生成時に対象モデルへ `use` を挿入する。モデルを再生成しても手書き部分が消えないよう、この仕組みに従うこと。

### フロントエンドアセット

ソースは `resources/js/*.es`（Babel→terser で `*.min.js`）と `resources/css/*.scss`（sass→autoprefixer→cssnano で `*.min.css`）。ビルド成果物はコミットされず `make` で生成され、`assets/*Asset.php` の AssetBundle が `sourcePath = '@app/resources'` から `*.min.*` を参照する。`.es`/`.scss` を編集したら `make` が必要。Bootstrap/jQuery 等の Yii 標準バンドルは `config/components/web/asset-manager/bundles/` で npm 版に差し替えている（`@bower`/`@npm` はどちらも `node_modules` を指す）。

### i18n

アプリの言語は `en-US`（ソース言語）と `ja-JP`。`helpers/ApplicationLanguage.php` が bootstrap でクエリパラメータ `_lang`・Cookie・ブラウザ設定から言語を決定する（有効言語は `language` テーブル）。翻訳は gettext 形式の `messages/ja/messages.po`。`make` 時に `./yii message/extract` が `Yii::t()` 呼び出しを抽出して `.po` を上書き更新し、`msgfmt` で `.mo` を生成する。メッセージ文字列を追加・変更したら `.po` に訳を入れること。

### Web 側

`config/bootstrap.php` で GridView / LinkPager / Pagination 等のデフォルト設定を DI コンテナに登録している。UI の部品は `widgets/` に集約されており、ビューは主にウィジェットの組み合わせ。`formatters/CompressiveHtmlResponseFormatter.php` が HTML レスポンスを圧縮する。

## コーディング規約

- 全 PHP ファイルは `declare(strict_types=1);` と既存と同じ著作権ヘッダを持つ。グローバル関数・定数は `use function` / `use const` で明示的にインポートする慣習がある。
- PHPStan は level max。mixed を扱う箇所は `helpers/TypeHelper.php`（`shouldBeString`, `shouldBeArray` 等）で型を絞る慣習がある。
- `migrations/` は名前空間なし・クラス名規約の例外扱い（`.phpcs.xml`）。
