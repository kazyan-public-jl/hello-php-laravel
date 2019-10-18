# LaravelでAPIを叩く！

PHPフレームワークである laravel のルーティング
を利用して、パラメータのやりとりを理解する！

引用部分のほとんどは https://readouble.com/laravel/5.8/ja/routing.html から引用したものです。

**結論**

1. routes/api.php にルーティング `/api/get_hello_message` を追加する
2. `php artisan serve` でサーバーを起動する

# ルーティング

## ルーティングについて理解する

ドキュメント日本語訳の「[ルーティング](https://readouble.com/laravel/5.8/ja/routing.html)」によると、

> 一番基本のLaravelルートはURIと「クロージャ」により定義され、単純で記述しやすいルートの定義方法を提供しています。

と定義されていて、基本形は以下のようになるようだ。

```php
Route::get('foo', function () {
    return 'Hello World';
});
```

## Webルーティング routes/web.php

> routes/web.phpファイルで、Webインターフェイスのルートを定義します。定義されたルートはwebミドルウェアグループにアサインされ、セッション状態やCSRF保護などの機能が提供されます。

> ほとんどのアプリケーションでは、routes/web.phpファイルからルート定義を始めます。routes/web.php中で定義されたルートは、ブラウザで定義したルートのURLを入力することでアクセスします。

> 次のルートはブラウザからhttp://your-app.test/userでアクセスします。

```php
Route::get('/user', 'UserController@index');
```

## APIルーティング routes/api.php

> routes/api.php中のルートはステートレスで、apiミドルウェアグループにアサインされます。

> routes/api.phpファイル中で定義したルートはRouteServiceProviderにより、ルートグループの中にネストされます。このグループには、/apiのURIが自動的にプレフィックスされ、それによりこのファイル中の全ルートにわざわざ指定する必要はありません。

## 使用可能なルート定義メソッド

ルータはHTTP動詞に対応してルートを定義できるようにしています。
```php
Route::get($uri, $callback);
Route::post($uri, $callback);
Route::put($uri, $callback);
Route::patch($uri, $callback);
Route::delete($uri, $callback);
Route::options($uri, $callback);
```
複数メソッドに適用する場合
```php
Route::match(['get', 'post'], '/', function () {
    //
});
```
全てのメソッドに適用する場合
```php
Route::any('/', function () {
    //
});
```
これは組み合わせることができるのだろうか？

## CSRF保護

CSRF(Cross-Site Request Forgeries)保護と言い、ユーザーが予期しないリクエストの実行を防止する仕組みです。

bladeテンプレートの `@csrf` ディレクティブでセットできます。

> webルートファイル中で定義され、POST、PUT、DELETEルートへ送信されるHTMLフォームはすべて、CSRFトークンフィールドを含んでいる必要があります。含めていないと、そのリクエストは拒否されます。

## リダイレクト処理

> 他のURIへリダイレクトするルートを定義する場合は、Route::redirectメソッドを使用します。
```php
Route::redirect('/here', '/there'); // これは標準で302を返す
Route::redirect('/here', '/there', 301); // リクエストステータスを任意に変更できる

Route::permanentRedirect('/here', '/there'); // これは標準で301を返す
```

## ビュールート

ルートからビューを返すだけの場合は、 `Route::view` メソッドを使用します。

```php
// URIと
Route::view('/welcome', 'welcome');

// 第３引数にパラメータをつけることもできる
Route::view('/welcome', 'welcome', ['name' => 'Taylor']);
```

# ルートパラメータ

ルーターによるルート登録において、いくつかのやり方で変数を登録できます。

**必須パラメータ {id}**

```php
Route::get('user/{id}', function ($id) {
    return 'User '.$id;
});
```

複数のパラメータを受け取ることもできます
```php
Route::get('posts/{post}/comments/{comment}', function ($postId, $commentId) {
    //
});
```

**任意パラメータ {name?}**

任意パラメータの場合は、該当するパラメータをコールバック関数内では初期値をセットしておく必要がある！

```php
Route::get('user/{name?}', function ($name = null) {
    return $name;
});

Route::get('user/{name?}', function ($name = 'John') {
    return $name;
});
```


-----

# API実行までの流れ

リクエストするURLをphpで定義し、そこに何かしらの方法でURLへアクセスすると良さそうだ。

結論としては

1. routes/api.php でAPIルーティングを定義して、
2. views/apitest.blade.php にリンクを設置し、
3. 発火したリクエストの返り値を受け取り
4. その返り値でメッセージを変更する

以上でAPI実行を確認としよう。

設置するリンクは
- GET  `/api/message` => hello0 を返す
- GET  `/api/message/hello1` => hello1 を返す
- POST `/api/message?message=hello2` => hello2 を返す

## URLからのAPIルーティングは api.php

まずブラウザ以外でアクセスした場合は `routes/api.php` のルールで振り分けられる

テストしたいAPIルーティングをそれぞれ設置する
```php
// GET 引数なし
Route::get('/message', function (Request $request) {
    return 'hello0 messge.';
});
// GET 引数付き
Route::get('/message/{text}', function (Request $request, $text = null) {
    return $text . ' messge.';
});

// POST
Route::post('/message', function (Request $request) {
    $text = $request->input('message');
    $result = array(
        'message' => $text
    );
    return json_encode($result);
});
```

## views/apitest.blade.php にリンクを設置する

ページのビューを `view/welcome.blade.php` に定義する。

リクエストを発火するリンクを設置する。
```html
<!-- GETリクエスト -->
<a href="/api/message">get message hello0</a>
<a href="/api/message/hello01">get message hello01</a>

<!-- POSTリクエスト -->
<form id='test' name='test' action="/api/message" method="post">
    <input type="hidden" name="message" value='hello02' />
    <input id='btn' type="button" value="post message">
</form>
```
ここまでで、各リンクをクリックすれば、その結果が表示された画面になることが確認できた。

## 発火したリクエストの返り値を受け取る

通信のたびに画面遷移するのは今時のページではないですね。
ページ遷移が発生しない非同期通信用の処理を検証しました。

### 1. 発火用ボタンを設置

```html
<form id='testAjax' name='testAjax' action="/api/message" method="post">
    <input type="hidden" name="message" value='hello03' />
    <input id='btnAjax' type="button" value="postAjax">
</form>
```

### 2. xHttpRequest を発行する

xHttpRequestはページの読込みなしにサーバーと通信をできる仕組みです。
この仕組みを利用して、ボタンをクリックしたらテキストボックスの中身が変わるようにします。

> [xHttpRequest | MDN web docs](https://developer.mozilla.org/ja/docs/Web/API/XMLHttpRequest)

まずはボタンエレメント(#btnAjax)にイベントトリガー(submitAjaxHandler)を登録します。
```js
window.onload = function(){
    // イベント登録
    let submitAjaxElement = document.getElementById('btnAjax');
    submitAjaxElement.addEventListener('click', submitAjaxHandler);
}
```
続いて、クリックされたエレメントを抽出し、
そこから親のFORMエレメントを見つけ、
リクエスト用のURLやメッセージを取得します。
```js
function submitAjaxHandler(event){
    // 通信情報を整理
    const targetElement = event.currentTarget;
    const formElement   = findParentForm(targetElement);
    const url     = formElement.action;
    const message = formElement.querySelector('input[name="message"]').value;

    //** 以降に通信処理を記述予定 **//
}
```
親のFORMを見つける処理はこちら。
クリックしたHTMLElement.parentNodeを再帰的に確認して、`tagName==FORM` のHTMLElementを返す。
```js
// 親FORMエレメントを再帰的に探す
function findParentForm(elem){ 
    const parent = elem.parentNode; 
    if(parent && parent.tagName != 'FORM'){
        parent = findParentForm(parent);
    }
    return parent;
}

// 親FORMが見つかったらそのエレメントを返す
function getParentForm( elem ){
    const parentForm = findParentForm(elem);
    if(parentForm){
        return parentForm;
    }else{
        return undefined;
    }
}
```
最後に xHttpRequest でリクエストを送信し、
値が返ってきたら結果格納用のテキストエリアに追記して完了とする。
```js
// 非同期通信オブジェクトを定義
const req = new XMLHttpRequest();
// 非同期通信を設定
req.open('POST', url, true);
req.setRequestHeader(
    'content-type',
    'application/x-www-form-urlencoded;charset=UTF-8'
);
// 非同期通信を送信, リクエストの返事で onreadystatechange が発火する
req.send('message='+message);
```

## 発火したリクエストの返り値を受け取り、返り値でテキストフォームを書き換える

`req.send()` で送ったリクエストが返ってくると `req.onreadystatechange` イベントが発火する。

レスポンス結果によって何かしたい場合はこのイベントに処理を記載すればよく、
レスポンス内容は `req.responsteText` にテキストデータとして返ってきている。

なのであとはテキストフォームのvalueにレスポンス内容を設定すればいい。
```js
// レスポンスがあった時に発火するメソッド
req.onreadystatechange = function() {
    // 書き換えるテキストフォームを特定する
    let messageElement = document.getElementById('ResultMessage');
    if (req.readyState == 4) { // 通信の完了時
        if (req.status == 200) { // 通信の成功時
            // レスポンス結果を #ResultMessage に表示する
            messageElement.value = req.responseText;
        }
    }else{
        messageElement.value = "通信中...";
    }
}
```

以上で LaravelのAPIを利用し、返ってきた値を利用して画面要素を書き換えるサンプルが作成できた！やったね！

ルーティングの基本的な遷移ができたので次なる興味は

- [ビュー](https://readouble.com/laravel/5.8/ja/views.html)
- [リクエスト](https://readouble.com/laravel/5.8/ja/requests.html)
- [レスポンス](https://readouble.com/laravel/5.8/ja/responses.html)
- [コントローラ](https://readouble.com/laravel/5.8/ja/controllers.html)
- [テスト](https://readouble.com/laravel/5.8/ja/testing.html)

いっぱいだ・・・。
しかし今みたいな感じで一つずつ試していこう。

-----

# 関連ページ

- [Laravel 公式](https://laravel.com/)
- [Laravel 5.8 ルーティング](https://readouble.com/laravel/5.8/ja/routing.html)
- [Laravel 5.8 CSRF保護](https://readouble.com/laravel/5.8/ja/csrf.html)
- [xHttpRequest | MDN web docs](https://developer.mozilla.org/ja/docs/Web/API/XMLHttpRequest)
