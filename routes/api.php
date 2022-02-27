<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request)
{
    return $request->user();
});

// 会員登録
Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register')->name('register');

// ログイン
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login')->name('login');

// ログアウト
Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

// ログインユーザー
Route::get('/user', fn () => Auth::user())->name('user');

// 写真投稿
Route::post('/photos', 'App\Http\Controllers\PhotoController@create')->name('photo.create');

// 写真一覧
Route::get('/photos', 'App\Http\Controllers\PhotoController@index')->name('photo.index');

// 写真詳細
Route::get('/photos/{id}', 'App\Http\Controllers\PhotoController@show')->name('photo.show');

// コメント
Route::post('/photos/{photo}/comments', 'App\Http\Controllers\PhotoController@addComment')->name('photo.comment');

// いいね
Route::put('/photos/{id}/like', 'App\Http\Controllers\PhotoController@like')->name('photo.like');

// いいね解除
Route::delete('/photos/{id}/like', 'App\Http\Controllers\PhotoController@unlike');

// トークンリフレッシュ
Route::get('/reflesh-token', function (Illuminate\Http\Request $request)
{
    $request->session()->regenerateToken();

    return response()->json();
});
