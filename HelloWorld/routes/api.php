<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// GET
Route::get('/message', function (Request $request) {
    return 'hello0 messge.';
});
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
