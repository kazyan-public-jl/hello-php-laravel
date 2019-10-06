# hello-php-laravel

PHPフレームワークである laravel のインストールからHello Worldまで

# インストール

## PHP7.2のインストール

Laravelの推奨環境がPHP7.2だったので、そのバージョンをインストールする

brew を使ってPHP7.2をインストールする
```sh
brew install php@7.2
```

環境変数にパスを通して、ターミナルを再起動して反映させる
```sh
echo 'export PATH="$(brew --prefix php@7.2)/bin:$PATH"' >> ~/.bash_profile
```

## Composer のインストール

Laravel の依存関係を管理する [Composer](https://getcomposer.org/) が必要らしいのでインストールする

macOSでは、 `brew` を使ってインストールが楽でした。
```
brew install composer
```


公式のコマンドにしたがってインストールしたが、 Command Not Found になった。
PATHの設定を正しく行おうと公式に書いてったが、どうパスを繋げたら良いか読み解けなかった。
```sh
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```


## Laravel のインストール

Composerを使って、Laravelのインストールを行う

```sh
composer global require laravel/installer
```

ComposerのbinがLaravelから実行できるように `$PATH` を通しておく
```sh
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bash_profile
```

# Laravelプロジェクトを作成する

## Laravel Installer で新規プロジェクトを作成する方法

`laravel new HelloWorld` で HelloWorld フォルダ以下に Laravel プロジェクトが作成される
```sh
laravel new HelloWorld
```

## Composer で新規プロジェクトを作成する方法

上記コマンドの代わりに, composer の `create-project` コマンドでも新規プロジェクトを作成できる
```sh
composer create-project --prefer-dist laravel/laravel blog
```

# ローカルサーバーを起動する

PHP環境があるなら、 `serve` コマンドでローカルサーバーを起動できる

起動後に `localhost:8000` へアクセスすればアプリケーションを確認できる

```sh
cd HelloWorld/
php artisan serve
```

ひとまず Laravel のデフォルト画面が表示されればOK!

# HelloWorld を仕込む

執筆中...


# 関連ページ

- [Laravel 公式](https://laravel.com/)
- [macOSに Laravel 5.6 をインストールする手順をまとめてみる](https://qiita.com/igz0/items/bd5ab0aedc75d8476c76)
- 