<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/info',function () {
    phpinfo();
});

Route::get('/','Index\IndexController@index');


//微信开发
Route::get('/wx','WeiXin\WxController@wx');
Route::post('/wx','WeiXin\WxController@receiv');
Route::get('/wx/media','WeiXin\WxController@getMedia');
Route::get('/wx/menu','WeiXin\WxController@createMenu');

//微信公众号
Route::get('/vote','VoteController@index');     //微信投票
