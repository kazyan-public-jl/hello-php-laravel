# LaravelでHelloWorldまでを実現する！

PHPフレームワークである laravel のインストールして、画面上に「Hello World!」を表示するまでの軌跡を残す

**結論**

1. Laravelをインストールする
2. views/ に表示用HTMLを作成する
3. routes/web.php にルーティング `/helloworld` を追加する
4. `php artisan serve` でサーバーを起動する
5. `http://localhost:8000/helloworld` にアクセスする

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

※なお、公式のコマンドにしたがってインストールしたが、 Command Not Found になったので上記の通り brew でトライした。
公式の案内は下記でした。
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

## その他

### publicディレクトリ

> Webサーバのドキュメント／Webルートがpublicディレクトリになるように設定してください。



### configディレクトリ

> フレームワークで使用する設定ファイルは全てconfigディレクトリ下に設置しています。
> それぞれのオプションにコメントがついていますので、使用可能なオプションを理解するため、ファイル全体に目を通しておくのが良いでしょう。

```sh
$ ls -l config/
app.php
auth.php
broadcasting.php
cache.php
database.php
filesystems.php
hashing.php
logging.php
mail.php
queue.php
services.php
session.php
view.php
```

### パーミッション設定

> Laravelをインストールした後に、多少のパーミッションの設定が必要です。storage下とbootstrap/cacheディレクトリをWebサーバから書き込み可能にしてください。設定しないとLaravelは正しく実行されません。

```sh
chmod 777 bootstrap/cache/
chmod 777 storage/
```

### アプリケーションキーの設定

> アプリケーションキーが設定されていなければ、ユーザーセッションや他の暗号化済みデーターは安全ではありません！
> ComposerかLaravelインストーラを使ってインストールしていれば、php artisan key:generateコマンドにより、既に設定されています。

Laravelインストーラを使用してインストールしたので、既に設定されていた。

### その他の設定

> config/app.phpファイルと、その中の記述を確認しておいたほうが良いでしょう。
> アプリケーションに合わせ変更したい、timezoneやlocalのような多くのオプションが含まれています。

```php
'timezone' => 'Asia/Tokyo',
'locale' => 'ja',
```

## Webサーバー設定

今回は ApacheもNginxも使ってないので特に指定してない。
ただ、Nginxは使いそうなので日本語訳からのメモを残しておく。

### Nginx

> Nginxを使用する場合は、全てのリクエストがindex.phpフロントコントローラへ集まるように、サイト設定に以下のディレクティブを使用します。

```sh
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

-----

# 画面表示までの流れ

> `php artisan serve`

で画面表示できることがわかったので、Hello World! 表示のために画面表示までの流れを追っていく。

結論としては routes/web.php でルーティングして、 views/welcome.blade.php が表示内容だった。

## URLからのルーティングは web.php

まずWebブラウザでアクセスした場合は `routes/web.php` のルールで振り分けられる
最初は `/` つまり `http://localhost:8080/` にアクセスした場合に表示するものだけが定義されている
ここで `view('welcome')` はviewフォルダ以下の welcome.blade.php を呼び出す記述になっている

```php
Route::get('/', function () {
    return view('welcome');
});
```

## 表示HTMLの記述形式は blade.php

実際のHTMLが記述されていたのは `view/welcome.blade.php` だった。
Smartyのようなテンプレート言語 [blade](https://readouble.com/laravel/5.5/ja/blade.html) を使っているようだ。

### blade

> Bladeビューには `.blade.php` ファイル拡張子を付け、通常は `resources/views` ディレクトリの中に設置します。

**記述ルール概要**

レイアウト定義
- `@section('section_name')`: セクション名をつけてHTML内容を定義する
- `@endsection`: セクション定義の終わりを示す
- `@show`: セクション定義の終わりを示しつつ、その場にレンダリングしたい場合は、これを `@endsection` の代わりに使う。
- `@yield('section_name')`: 定義されたセクション名を表示する

レイアウト拡張
- `@extends('layouts.parent')`: 継承元のファイルを指定できます. ← views/layouts/parent.blade.php を指定したい場合
- `@section('section_name','Section Name')`: 継承元のレイアウトを上書きする場合、文字列のみで上書きする場合はこう記述できる
- `@section('section_name') hogehoge @endsection`: 継承元のレイアウトを上書きする場合、基本的には @section 〜 @endsection でくくる
- `@@parent`: 親セクションの記述を挿入したい場合は @@parent を記述する

# Hello World を表示する

ようやく冒頭でやりたい「Hello World!」を表示するための情報が揃った。
やることは次の２つ。

1. views/ に Hello World 表示用の blade ファイルを追加
2. routes/web.php にURLルーティングのルールを追加

やっていこう！

## views/ に Hello World 表示用の blade ファイルを追加

views/ フォルダに `helloworld.blade.php` を作成する。
welcome.blade.php から余分なHTML要素を削って以下のようにした
```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Hello World</title>
    </head>
    <body>
        <div class="content">
            <p>Laravel Hello World!!<p>
        </div>
    </body>
</html>
```


## routes/web.php にURLルーティングのルールを追加

routes/web.php に次の記述を追加
```php
Route::get('/helloworld', function () {
    return view('helloworld');
});
```

## http://localhost:8000/helloworld にアクセス

`http://localhost:8000/helloworld` にアクセスして表示を確かめる

```html
Laravel Hello World!!
```

と表示された！やったね！

以上の流れが理解できた後はフロントエンドからAPIを利用するために [ルーティング](https://readouble.com/laravel/5.8/ja/routing.html) について学んでみたい。

-----

# 関連ページ

- [Laravel 公式](https://laravel.com/)
- [macOSに Laravel 5.6 をインストールする手順をまとめてみる](https://qiita.com/igz0/items/bd5ab0aedc75d8476c76)
- [Laravel ドキュメント一覧](https://readouble.com/laravel/)
- [Laravel 5.5 Bladeテンプレート](https://readouble.com/laravel/5.5/ja/blade.html)
- [Laravel 5.8 ルーティング](https://readouble.com/laravel/5.8/ja/routing.html)